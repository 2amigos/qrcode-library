<?php
/**
 * @copyright Copyright (c) 2013-15 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\lib;

use yii\base\InvalidParamException;
use yii\base\Object;

/**
 * Class Code
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\lib
 */
class Code extends Object
{
    /**
     * @var int version
     */
    public $version;
    /**
     * @var int width
     */
    public $width;
    /**
     * @var array the data
     */
    public $data;
    /**
     * @var bool whether to benchmark or not the process
     */
    public $benchmark = false;

    /**
     * @param Input $input
     * @param $mask
     *
     * @return $this|null
     * @throws \yii\base\InvalidParamException
     */
    public function encodeMask(Input $input, $mask)
    {
        if ($input->getVersion() < 0 || $input->getVersion() > Enum::QRSPEC_VERSION_MAX) {
            throw new InvalidParamException('wrong version');
        }
        if ($input->getErrorCorrectionLevel() > Enum::QR_ECLEVEL_H) {
            throw new InvalidParamException('wrong level');
        }

        $raw = new RawCode($input);

        if ($this->benchmark)
            Tools::markTime('after_raw');

        $version = $raw->version;
        $width = Specifications::getWidth($version);
        $frame = Specifications::newFrame($version);

        $filler = new FrameFiller($width, $frame);

        if (is_null($filler))
            return null;

        // inteleaved data and ecc codes
        for ($i = 0; $i < $raw->dataLength + $raw->eccLength; $i++) {
            $code = $raw->getCode();
            $bit = 0x80;
            for ($j = 0; $j < 8; $j++) {
                $addr = $filler->next();
                $filler->setFrameAt($addr, 0x02 | (($bit & $code) != 0));
                $bit = $bit >> 1;
            }
        }

        if ($this->benchmark)
            Tools::markTime('after_filler');

        unset($raw);

        // remainder bits
        $j = Specifications::getRemainder($version);
        for ($i = 0; $i < $j; $i++) {
            $addr = $filler->next();
            $filler->setFrameAt($addr, 0x02);
        }

        $frame = $filler->frame;
        unset($filler);

        // masking
        $maskObj = new Mask();

        if ($mask < 0) {

            if (Enum::QR_FIND_BEST_MASK)
                $masked = $maskObj->mask($width, $frame, $input->getErrorCorrectionLevel());
            else
                $masked = $maskObj->makeMask(
                    $width,
                    $frame,
                    (intval(Enum::QR_DEFAULT_MASK) % 8),
                    $input->getErrorCorrectionLevel()
                );

        } else
            $masked = $maskObj->makeMask($width, $frame, $mask, $input->getErrorCorrectionLevel());

        if (is_null($masked))
            return null;

        if ($this->benchmark)
            Tools::markTime('after_mask');

        $this->version = $version;
        $this->width = $width;
        $this->data = $masked;

        return $this;
    }

    /**
     * @param Input $input
     *
     * @return $this|null
     */
    public function encodeInput(Input $input)
    {
        return $this->encodeMask($input, -1);
    }

    /**
     * @param $string
     * @param $version
     * @param $level
     *
     * @return $this|null
     * @throws \yii\base\InvalidParamException
     */
    public function encodeString8bit($string, $version, $level)
    {
        if ($string == null) {
            throw new InvalidParamException('empty string!');
        }

        $input = new Input($version, $level);

        if ($input == null)
            return null;

        $ret = $input->append($input, Enum::QR_MODE_8, strlen($string), str_split($string));
        if ($ret < 0) {
            unset($input);
            return null;
        }
        return $this->encodeInput($input);
    }

    /**
     * @param $string
     * @param $version
     * @param $level
     * @param $hint
     * @param $caseSensitive
     *
     * @return $this|null
     * @throws \yii\base\InvalidParamException
     */
    public function encodeString($string, $version, $level, $hint, $caseSensitive)
    {

        if ($hint != Enum::QR_MODE_8 && $hint != Enum::QR_MODE_KANJI) {
            throw new InvalidParamException('bad hint');
        }

        $input = new Input($version, $level);

        if ($input == null)
            return null;

        $ret = Split::splitStringToQrInput($string, $input, $hint, $caseSensitive);

        if ($ret < 0) {
            return null;
        }

        return $this->encodeInput($input);
    }
}