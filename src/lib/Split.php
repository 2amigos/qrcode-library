<?php
/**
 * @copyright Copyright (c) 2013-15 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\lib;

use yii\base\InvalidParamException;

/**
 * Class Split
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\lib
 */
class Split
{

    /**
     * @var string
     */
    public $dataStr = '';
    /**
     * @var
     */
    public $input;
    /**
     * @var
     */
    public $modeHint;


    /**
     * @param $dataStr
     * @param $input
     * @param $modeHint
     */
    public function __construct($dataStr, $input, $modeHint)
    {
        $this->dataStr = $dataStr;
        $this->input = $input;
        $this->modeHint = $modeHint;
    }


    /**
     * @param $str
     * @param $pos
     *
     * @return bool
     */
    public static function isDigitAt($str, $pos)
    {
        return ($pos >= strlen($str))
            ? false
            : ((ord($str[$pos]) >= ord('0')) && (ord($str[$pos]) <= ord('9')));
    }


    /**
     * @param $str
     * @param $pos
     *
     * @return bool
     */
    public static function isAlNumAt($str, $pos)
    {
        return ($pos >= strlen($str))
            ? false
            : (Input::lookAnTable(ord($str[$pos])) >= 0);
    }


    /**
     * @param $pos
     *
     * @return int
     */
    public function identifyMode($pos)
    {
        if ($pos >= strlen($this->dataStr)) {
            return Enum::QR_MODE_NULL;
        }

        $c = $this->dataStr[$pos];

        if (static::isDigitAt($this->dataStr, $pos)) {
            return Enum::QR_MODE_NUM;
        } else if (static::isAlNumAt($this->dataStr, $pos)) {
            return Enum::QR_MODE_AN;
        } else if ($this->modeHint == Enum::QR_MODE_KANJI) {

            if ($pos + 1 < strlen($this->dataStr)) {
                $d = $this->dataStr[$pos + 1];
                $word = (ord($c) << 8) | ord($d);
                if (($word >= 0x8140 && $word <= 0x9ffc) || ($word >= 0xe040 && $word <= 0xebbf)) {
                    return Enum::QR_MODE_KANJI;
                }
            }
        }

        return Enum::QR_MODE_8;
    }


    /**
     * @return int
     */
    public function eatNum()
    {
        $ln = Specifications::lengthIndicator(Enum::QR_MODE_NUM, $this->input->getVersion());

        $p = 0;
        while (static::isDigitAt($this->dataStr, $p)) {
            $p++;
        }

        $run = $p;
        $mode = $this->identifyMode($p);

        if ($mode == Enum::QR_MODE_8) {
            $dif = Input::estimateBitsModeNum($run) + 4 + $ln
                + Input::estimateBitsMode8(1) // + 4 + l8
                - Input::estimateBitsMode8($run + 1); // - 4 - l8
            if ($dif > 0) {
                return $this->eat8();
            }
        }
        if ($mode == Enum::QR_MODE_AN) {
            $dif = Input::estimateBitsModeNum($run) + 4 + $ln
                + Input::estimateBitsModeAn(1) // + 4 + la
                - Input::estimateBitsModeAn($run + 1); // - 4 - la
            if ($dif > 0) {
                return $this->eatAn();
            }
        }

        $ret = $this->input->append(Enum::QR_MODE_NUM, $run, str_split($this->dataStr));
        if ($ret < 0) {
            return -1;
        }

        return $run;
    }


    /**
     * @return int
     */
    public function eatAn()
    {
        $la = Specifications::lengthIndicator(Enum::QR_MODE_AN, $this->input->getVersion());
        $ln = Specifications::lengthIndicator(Enum::QR_MODE_NUM, $this->input->getVersion());

        $p = 0;

        while (static::isAlNumAt($this->dataStr, $p)) {
            if (static::isDigitAt($this->dataStr, $p)) {
                $q = $p;
                while (static::isDigitAt($this->dataStr, $q)) {
                    $q++;
                }

                $dif = Input::estimateBitsModeAn($p) // + 4 + la
                    + Input::estimateBitsModeNum($q - $p) + 4 + $ln
                    - Input::estimateBitsModeAn($q); // - 4 - la

                if ($dif < 0)
                    break;
                else
                    $p = $q;
            } else
                $p++;
        }

        $run = $p;

        if (!static::isAlNumAt($this->dataStr, $p)) {
            $dif = Input::estimateBitsModeAn($run) + 4 + $la
                + Input::estimateBitsMode8(1) // + 4 + l8
                - Input::estimateBitsMode8($run + 1); // - 4 - l8
            if ($dif > 0) {
                return $this->eat8();
            }
        }

        $ret = $this->input->append(Enum::QR_MODE_AN, $run, str_split($this->dataStr));
        if ($ret < 0) {
            return -1;
        }

        return $run;
    }


    /**
     * @return int
     */
    public function eatKanji()
    {
        $p = 0;

        while ($this->identifyMode($p) == Enum::QR_MODE_KANJI) {
            $p += 2;
        }

        $ret = $this->input->append(Enum::QR_MODE_KANJI, $p, str_split($this->dataStr));
        if ($ret < 0) {
            return -1;
        }

        return $ret;
    }


    /**
     * @return int
     */
    public function eat8()
    {
        $la = Specifications::lengthIndicator(Enum::QR_MODE_AN, $this->input->getVersion());
        $ln = Specifications::lengthIndicator(Enum::QR_MODE_NUM, $this->input->getVersion());

        $p = 1;
        $dataStrLen = strlen($this->dataStr);

        while ($p < $dataStrLen) {

            $mode = $this->identifyMode($p);
            if ($mode == Enum::QR_MODE_KANJI) {
                break;
            }
            if ($mode == Enum::QR_MODE_NUM) {
                $q = $p;
                while (static::isDigitAt($this->dataStr, $q)) {
                    $q++;
                }

                $dif = Input::estimateBitsMode8($p) // + 4 + l8
                    + Input::estimateBitsModeNum($q - $p) + 4 + $ln
                    - Input::estimateBitsMode8($q); // - 4 - l8
                if ($dif < 0) {
                    break;
                } else {
                    $p = $q;
                }
            } else if ($mode == Enum::QR_MODE_AN) {
                $q = $p;
                while (static::isAlNumAt($this->dataStr, $q)) {
                    $q++;
                }

                $dif = Input::estimateBitsMode8($p) // + 4 + l8
                    + Input::estimateBitsModeAn($q - $p) + 4 + $la
                    - Input::estimateBitsMode8($q); // - 4 - l8
                if ($dif < 0)
                    break;
                else
                    $p = $q;
            } else
                $p++;
        }

        $run = $p;
        $ret = $this->input->append(Enum::QR_MODE_8, $run, str_split($this->dataStr));

        if ($ret < 0)
            return -1;

        return $run;
    }


    /**
     * @return int
     */
    public function splitString()
    {
        while (strlen($this->dataStr) > 0) {
            if ($this->dataStr == '') {
                return 0;
            }

            $mode = $this->identifyMode(0);

            switch ($mode) {
                case Enum::QR_MODE_NUM:
                    $length = $this->eatNum();
                    break;
                case Enum::QR_MODE_AN:
                    $length = $this->eatAn();
                    break;
                case Enum::QR_MODE_KANJI:
                    if ($this->modeHint == Enum::QR_MODE_KANJI) {
                        $length = $this->eatKanji();
                    } else {
                        $length = $this->eat8();
                    }
                    break;
                default:
                    $length = $this->eat8();
                    break;
            }

            if ($length == 0) {
                return 0;
            }
            if ($length < 0) {
                return -1;
            }

            $this->dataStr = substr($this->dataStr, $length);
        }
    }


    /**
     * @return string
     */
    public function toUpper()
    {
        $stringLen = strlen($this->dataStr);
        $p = 0;

        while ($p < $stringLen) {
            $mode = static::identifyMode(substr($this->dataStr, $p), $this->modeHint);
            if ($mode == Enum::QR_MODE_KANJI) {
                $p += 2;
            } else {
                if (ord($this->dataStr[$p]) >= ord('a') && ord($this->dataStr[$p]) <= ord('z'))
                    $this->dataStr[$p] = chr(ord($this->dataStr[$p]) - 32);
                $p++;
            }
        }

        return $this->dataStr;
    }


    /**
     * @param $string
     * @param Input $input
     * @param $modeHint
     * @param bool $caseSensitive
     *
     * @return int
     * @throws \yii\base\InvalidParamException
     */
    public static function splitStringToQrInput($string, Input $input, $modeHint, $caseSensitive = true)
    {
        if (is_null($string) || $string == '\0' || $string == '')
            throw new InvalidParamException('empty string!!!');

        $split = new Split($string, $input, $modeHint);

        if (!$caseSensitive) {
            $split->toUpper();
        }

        return $split->splitString();
    }

}