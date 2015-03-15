<?php
/**
 * @copyright Copyright (c) 2013 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\lib;

use yii\base\InvalidConfigException;
use yii\base\InvalidParamException;

/**
 * Class Input
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\lib
 */
class Input
{
    /**
     * @var array the data items
     */
    public $items;
    /**
     * @var array
     */
    public static $anTable = [
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        36,
        -1,
        -1,
        -1,
        37,
        38,
        -1,
        -1,
        -1,
        -1,
        39,
        40,
        -1,
        41,
        42,
        43,
        0,
        1,
        2,
        3,
        4,
        5,
        6,
        7,
        8,
        9,
        44,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        10,
        11,
        12,
        13,
        14,
        15,
        16,
        17,
        18,
        19,
        20,
        21,
        22,
        23,
        24,
        25,
        26,
        27,
        28,
        29,
        30,
        31,
        32,
        33,
        34,
        35,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1,
        -1
    ];
    /**
     * @var int version number
     */
    private $version;
    /**
     * @var int level of error correction
     */
    private $level;

    /**
     * @param int $version
     * @param int $level
     *
     * @throws \yii\base\InvalidConfigException
     */
    public function __construct($version = 0, $level = Enum::QR_ECLEVEL_L)
    {
        if ($version < 0 || $version > Enum::QRSPEC_VERSION_MAX || $level > Enum::QR_ECLEVEL_H) {
            throw new InvalidConfigException('Invalid version no');
        }
        $this->version = $version;
        $this->level = $level;
    }

    /**
     * @return int
     */
    public function getVersion()
    {
        return $this->version;
    }

    /**
     * @param $version
     *
     * @return int
     * @throws \yii\base\InvalidParamException
     */
    public function setVersion($version)
    {
        if ($version < 0 || $version > Enum::QRSPEC_VERSION_MAX) {
            throw new InvalidParamException('Invalid version number');
        }

        $this->version = $version;

        return 0;
    }

    /**
     * @return int
     */
    public function getErrorCorrectionLevel()
    {
        return $this->level;
    }

    /**
     * @param $level
     *
     * @return int
     * @throws \yii\base\InvalidParamException
     */
    public function setErrorCorrectionLevel($level)
    {
        if ($level > Enum::QR_ECLEVEL_H) {
            throw new InvalidParamException('Invalid ECLEVEL');
        }

        $this->level = $level;

        return 0;
    }

    /**
     * @param InputItem $entry
     */
    public function appendEntry(InputItem $entry)
    {
        $this->items[] = $entry;
    }

    /**
     * @param $mode
     * @param $size
     * @param $data
     *
     * @return int
     */
    public function append($mode, $size, $data)
    {
        try {
            $entry = new InputItem($mode, $size, $data);
            $this->items[] = $entry;
        } catch (\Exception $e) {
            return -1;
        }
        return 0;
    }

    /**
     * @param $size
     * @param $index
     * @param $parity
     *
     * @return int
     * @throws \yii\base\InvalidParamException
     */
    public function insertStructuredAppendHeader($size, $index, $parity)
    {
        if ($size > Enum::MAX_STRUCTURED_SYMBOLS)
            throw new InvalidParamException('insertStructuredAppendHeader wrong size');

        if ($index <= 0 || $index > Enum::MAX_STRUCTURED_SYMBOLS)
            throw new InvalidParamException('insertStructuredAppendHeader wrong index');

        $buf = array($size, $index, $parity);

        try {
            $entry = new InputItem(Enum::QR_MODE_STRUCTURE, 3, $buf);
            array_unshift($this->items, $entry);
        } catch (\Exception $e) {
            return -1;
        }
        return 0;
    }

    /**
     * @return int
     */
    public function calcParity()
    {
        $parity = 0;

        foreach ($this->items as $item) {
            if ($item->mode != Enum::QR_MODE_STRUCTURE) {
                for ($i = $item->size - 1; $i >= 0; $i--) {
                    $parity ^= $item->data[$i];
                }
            }
        }

        return $parity;
    }

    /**
     * @param $size
     * @param $data
     *
     * @return bool
     */
    public static function checkModeNum($size, $data)
    {
        for ($i = 0; $i < $size; $i++) {
            if ((ord($data[$i]) < ord('0')) || (ord($data[$i]) > ord('9'))) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $size
     *
     * @return float|int
     */
    public static function estimateBitsModeNum($size)
    {
        $w = (int)$size / 3;
        $bits = $w * 10;

        switch ($size - $w * 3) {
            case 1:
                $bits += 4;
                break;
            case 2:
                $bits += 7;
                break;
            default:
                break;
        }

        return $bits;
    }


    /**
     * @param $c
     *
     * @return int
     */
    public static function lookAnTable($c)
    {
        return (($c > 127) ? -1 : static::$anTable[$c]);
    }


    /**
     * @param $size
     * @param $data
     *
     * @return bool
     */
    public static function checkModeAn($size, $data)
    {
        for ($i = 0; $i < $size; $i++) {
            if (static::lookAnTable(ord($data[$i])) == -1) {
                return false;
            }
        }

        return true;
    }


    /**
     * @param $size
     *
     * @return int
     */
    public static function estimateBitsModeAn($size)
    {
        $w = (int)($size / 2);
        $bits = $w * 11;

        if ($size & 1) {
            $bits += 6;
        }

        return $bits;
    }


    /**
     * @param $size
     *
     * @return mixed
     */
    public static function estimateBitsMode8($size)
    {
        return $size * 8;
    }


    /**
     * @param $size
     *
     * @return int
     */
    public static function estimateBitsModeKanji($size)
    {
        return (int)(($size / 2) * 13);
    }


    /**
     * @param $size
     * @param $data
     *
     * @return bool
     */
    public static function checkModeKanji($size, $data)
    {
        if ($size & 1)
            return false;

        for ($i = 0; $i < $size; $i += 2) {
            $val = (ord($data[$i]) << 8) | ord($data[$i + 1]);
            if ($val < 0x8140
                || ($val > 0x9ffc && $val < 0xe040)
                || $val > 0xebbf
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param $mode
     * @param $size
     * @param $data
     *
     * @return bool
     */
    public static function check($mode, $size, $data)
    {
        if ($size <= 0)
            return false;

        switch ($mode) {
            case Enum::QR_MODE_NUM:
                return static::checkModeNum($size, $data);
                break;
            case Enum::QR_MODE_AN:
                return static::checkModeAn($size, $data);
                break;
            case Enum::QR_MODE_KANJI:
                return static::checkModeKanji($size, $data);
                break;
            case Enum::QR_MODE_8:
                return true;
                break;
            case Enum::QR_MODE_STRUCTURE:
                return true;
                break;

            default:
                break;
        }

        return false;
    }


    /**
     * @param $version
     *
     * @return float|int|mixed
     */
    public function estimateBitStreamSize($version)
    {
        $bits = 0;
        /** @var InputItem $item */
        foreach ($this->items as $item) {
            $bits += $item->estimateBitStreamSizeOfEntry($version);
        }

        return $bits;
    }


    /**
     * @return int
     */
    public function estimateVersion()
    {
        $version = 0;
        do {
            $prev = $version;
            $bits = $this->estimateBitStreamSize($prev);
            $version = Specifications::getMinimumVersion((int)(($bits + 7) / 8), $this->level);
            if ($version < 0)
                return -1;
        } while ($version > $prev);

        return $version;
    }


    /**
     * @param $mode
     * @param $version
     * @param $bits
     *
     * @return int
     */
    public static function lengthOfCode($mode, $version, $bits)
    {
        $payload = $bits - 4 - Specifications::lengthIndicator($mode, $version);
        switch ($mode) {
            case Enum::QR_MODE_NUM:
                $chunks = (int)($payload / 10);
                $remain = $payload - $chunks * 10;
                $size = $chunks * 3;
                if ($remain >= 7) {
                    $size += 2;
                } else if ($remain >= 4) {
                    $size += 1;
                }
                break;
            case Enum::QR_MODE_AN:
                $chunks = (int)($payload / 11);
                $remain = $payload - $chunks * 11;
                $size = $chunks * 2;
                if ($remain >= 6)
                    $size++;
                break;
            case Enum::QR_MODE_8:
                $size = (int)($payload / 8);
                break;
            case Enum::QR_MODE_KANJI:
                $size = (int)(($payload / 13) * 2);
                break;
            case Enum::QR_MODE_STRUCTURE:
                $size = (int)($payload / 8);
                break;
            default:
                $size = 0;
                break;
        }

        $maxsize = Specifications::maximumWords($mode, $version);
        if ($size < 0)
            $size = 0;
        if ($size > $maxsize)
            $size = $maxsize;

        return $size;
    }


    /**
     * @return int
     */
    public function createBitStream()
    {
        $total = 0;
        /** @var InputItem $item */
        foreach ($this->items as $item) {
            $bits = $item->encodeBitStream($this->version);

            if ($bits < 0)
                return -1;

            $total += $bits;
        }

        return $total;
    }


    /**
     * @return int
     * @throws \yii\base\InvalidConfigException
     */
    public function convertData()
    {
        $ver = $this->estimateVersion();
        if ($ver > $this->getVersion()) {
            $this->setVersion($ver);
        }

        for (; ;) {
            $bits = $this->createBitStream();

            if ($bits < 0)
                return -1;

            $ver = Specifications::getMinimumVersion((int)(($bits + 7) / 8), $this->level);
            if ($ver < 0) {
                throw new InvalidConfigException('Wrong version');
            } else if ($ver > $this->getVersion()) {
                $this->setVersion($ver);
            } else {
                break;
            }
        }

        return 0;
    }


    /**
     * @param BitStream $bStream
     *
     * @return int
     */
    public function appendPaddingBit(BitStream &$bStream)
    {
        $bits = $bStream->size();
        $maxWords = Specifications::getDataLength($this->version, $this->level);
        $maxBits = $maxWords * 8;

        if ($maxBits == $bits) {
            return 0;
        }

        if ($maxBits - $bits < 5) {
            return $bStream->appendNum($maxBits - $bits, 0);
        }

        $bits += 4;
        $words = (int)(($bits + 7) / 8);

        $padding = new BitStream();
        $ret = $padding->appendNum($words * 8 - $bits + 4, 0);

        if ($ret < 0)
            return $ret;

        $padLen = $maxWords - $words;

        if ($padLen > 0) {

            $padBuf = [];
            for ($i = 0; $i < $padLen; $i++) {
                $padBuf[$i] = ($i & 1) ? 0x11 : 0xec;
            }

            $ret = $padding->appendBytes($padLen, $padBuf);

            if ($ret < 0)
                return $ret;
        }

        $ret = $bStream->append($padding);

        return $ret;
    }


    /**
     * @return BitStream|null
     */
    public function mergeBitStream()
    {
        if ($this->convertData() < 0) {
            return null;
        }

        $bStream = new BitStream();

        foreach ($this->items as $item) {
            $ret = $bStream->append($item->bStream);
            if ($ret < 0) {
                return null;
            }
        }

        return $bStream;
    }


    /**
     * @return BitStream|null
     */
    public function getBitStream()
    {

        $bStream = $this->mergeBitStream();

        if ($bStream == null) {
            return null;
        }

        $ret = $this->appendPaddingBit($bStream);
        if ($ret < 0) {
            return null;
        }

        return $bStream;
    }


    /**
     * @return array|null
     */
    public function getByteStream()
    {
        $bStream = $this->getBitStream();
        if ($bStream == null) {
            return null;
        }

        return $bStream->toByte();
    }
}

