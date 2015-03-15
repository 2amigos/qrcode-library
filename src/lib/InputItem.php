<?php
/**
 * @copyright Copyright (c) 2013-15 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\lib;

use yii\base\InvalidConfigException;

/**
 * Class InputItem
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\lib
 */
class InputItem
{

    /**
     * @var
     */
    public $mode;
    /**
     * @var
     */
    public $size;
    /**
     * @var array
     */
    public $data;
    /**
     * @var null
     */
    public $bStream;

    /**
     * @param $mode
     * @param $size
     * @param $data
     * @param null $bStream
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct($mode, $size, $data, $bStream = null)
    {
        $setData = array_slice($data, 0, $size);

        if (count($setData) < $size) {
            $setData = array_merge($setData, array_fill(0, $size - count($setData), 0));
        }

        if (!Input::check($mode, $size, $setData)) {
            throw new InvalidConfigException('Error m:' . $mode . ',s:' . $size . ',d:' . join(',', $setData));
        }

        $this->mode = $mode;
        $this->size = $size;
        $this->data = $setData;
        $this->bStream = $bStream;
    }


    /**
     * @param $version
     *
     * @return int
     */
    public function encodeModeNum($version)
    {
        try {

            $words = (int)($this->size / 3);
            $bs = new BitStream();

            $val = 0x1;
            $bs->appendNum(4, $val);
            $bs->appendNum(Specifications::lengthIndicator(Enum::QR_MODE_NUM, $version), $this->size);

            for ($i = 0; $i < $words; $i++) {
                $val = (ord($this->data[$i * 3]) - ord('0')) * 100;
                $val += (ord($this->data[$i * 3 + 1]) - ord('0')) * 10;
                $val += (ord($this->data[$i * 3 + 2]) - ord('0'));
                $bs->appendNum(10, $val);
            }

            if ($this->size - $words * 3 == 1) {
                $val = ord($this->data[$words * 3]) - ord('0');
                $bs->appendNum(4, $val);
            } else if ($this->size - $words * 3 == 2) {
                $val = (ord($this->data[$words * 3]) - ord('0')) * 10;
                $val += (ord($this->data[$words * 3 + 1]) - ord('0'));
                $bs->appendNum(7, $val);
            }

            $this->bStream = $bs;

        } catch (\Exception $e) {
            return -1;
        }

        return 0;
    }


    /**
     * @param $version
     *
     * @return int
     */
    public function encodeModeAn($version)
    {
        try {
            $words = (int)($this->size / 2);
            $bs = new BitStream();

            $bs->appendNum(4, 0x02);
            $bs->appendNum(Specifications::lengthIndicator(Enum::QR_MODE_AN, $version), $this->size);

            for ($i = 0; $i < $words; $i++) {
                $val = (int)Input::lookAnTable(ord($this->data[$i * 2])) * 45;
                $val += (int)Input::lookAnTable(ord($this->data[$i * 2 + 1]));

                $bs->appendNum(11, $val);
            }

            if ($this->size & 1) {
                $val = Input::lookAnTable(ord($this->data[$words * 2]));
                $bs->appendNum(6, $val);
            }

            $this->bStream = $bs;

        } catch (\Exception $e) {
            return -1;
        }

        return 0;
    }


    /**
     * @param $version
     *
     * @return int
     */
    public function encodeMode8($version)
    {
        try {
            $bs = new BitStream();

            $bs->appendNum(4, 0x4);
            $bs->appendNum(Specifications::lengthIndicator(Enum::QR_MODE_8, $version), $this->size);

            for ($i = 0; $i < $this->size; $i++) {
                $bs->appendNum(8, ord($this->data[$i]));
            }

            $this->bStream = $bs;

        } catch (\Exception $e) {
            return -1;
        }

        return 0;
    }


    /**
     * @param $version
     *
     * @return int
     */
    public function encodeModeKanji($version)
    {
        try {

            $bs = new BitStream();

            $bs->appendNum(4, 0x8);
            $bs->appendNum(Specifications::lengthIndicator(Enum::QR_MODE_KANJI, $version), (int)($this->size / 2));

            for ($i = 0; $i < $this->size; $i += 2) {
                $val = (ord($this->data[$i]) << 8) | ord($this->data[$i + 1]);
                if ($val <= 0x9ffc)
                    $val -= 0x8140;
                else
                    $val -= 0xc140;

                $h = ($val >> 8) * 0xc0;
                $val = ($val & 0xff) + $h;

                $bs->appendNum(13, $val);
            }

            $this->bStream = $bs;

        } catch (\Exception $e) {
            return -1;
        }
        return 0;
    }


    /**
     * @return int
     */
    public function encodeModeStructure()
    {
        try {
            $bs = new BitStream();

            $bs->appendNum(4, 0x03);
            $bs->appendNum(4, ord($this->data[1]) - 1);
            $bs->appendNum(4, ord($this->data[0]) - 1);
            $bs->appendNum(8, ord($this->data[2]));

            $this->bStream = $bs;

        } catch (\Exception $e) {
            return -1;
        }
        return 0;
    }


    /**
     * @param $version
     *
     * @return float|int|mixed
     */
    public function estimateBitStreamSizeOfEntry($version)
    {

        if ($version == 0)
            $version = 1;

        switch ($this->mode) {
            case Enum::QR_MODE_NUM:
                $bits = Input::estimateBitsModeNum($this->size);
                break;
            case Enum::QR_MODE_AN:
                $bits = Input::estimateBitsModeAn($this->size);
                break;
            case Enum::QR_MODE_8:
                $bits = Input::estimateBitsMode8($this->size);
                break;
            case Enum::QR_MODE_KANJI:
                $bits = Input::estimateBitsModeKanji($this->size);
                break;
            case Enum::QR_MODE_STRUCTURE:
                return Enum::STRUCTURE_HEADER_BITS;
            default:
                return 0;
        }

        $l = Specifications::lengthIndicator($this->mode, $version);
        $m = 1 << $l;
        $num = (int)(($this->size + $m - 1) / $m);

        $bits += $num * (4 + $l);

        return $bits;
    }


    /**
     * @param $version
     *
     * @return int
     */
    public function encodeBitStream($version)
    {
        try {

            unset($this->bStream);
            $words = Specifications::maximumWords($this->mode, $version);

            if ($this->size > $words) {

                $st1 = new InputItem($this->mode, $words, $this->data);
                $st2 = new InputItem($this->mode, $this->size - $words, array_slice($this->data, $words));

                $st1->encodeBitStream($version);
                $st2->encodeBitStream($version);

                $this->bStream = new BitStream();
                $this->bStream->append($st1->bStream);
                $this->bStream->append($st2->bStream);

                unset($st1);
                unset($st2);
            } else {

                $ret = 0;

                switch ($this->mode) {
                    case Enum::QR_MODE_NUM:
                        $ret = $this->encodeModeNum($version);
                        break;
                    case Enum::QR_MODE_AN:
                        $ret = $this->encodeModeAn($version);
                        break;
                    case Enum::QR_MODE_8:
                        $ret = $this->encodeMode8($version);
                        break;
                    case Enum::QR_MODE_KANJI:
                        $ret = $this->encodeModeKanji($version);
                        break;
                    case Enum::QR_MODE_STRUCTURE:
                        $ret = $this->encodeModeStructure();
                        break;

                    default:
                        break;
                }

                if ($ret < 0)
                    return -1;
            }


        } catch (\Exception $e) {
            return -1;
        }

        return $this->bStream->size();
    }

}

