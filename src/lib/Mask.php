<?php
/**
 * @copyright Copyright (c) 2013-15 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\lib;

/**
 * Class Mask
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\lib
 */
class Mask
{
    /**
     * @var array
     */
    public $runLength = [];


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->runLength = array_fill(0, Enum::QRSPEC_WIDTH_MAX + 1, 0);
    }


    /**
     * @param $width
     * @param $frame
     * @param $mask
     * @param $level
     *
     * @return int
     */
    public function writeFormatInformation($width, &$frame, $mask, $level)
    {
        $blacks = 0;
        $format = Specifications::getFormatInfo($mask, $level);

        for ($i = 0; $i < 8; $i++) {
            if ($format & 1) {
                $blacks += 2;
                $v = 0x85;
            } else
                $v = 0x84;

            $frame[8][$width - 1 - $i] = chr($v);
            if ($i < 6)
                $frame[$i][8] = chr($v);
            else
                $frame[$i + 1][8] = chr($v);
            $format = $format >> 1;
        }

        for ($i = 0; $i < 7; $i++) {
            if ($format & 1) {
                $blacks += 2;
                $v = 0x85;
            } else
                $v = 0x84;

            $frame[$width - 7 + $i][8] = chr($v);
            if ($i == 0)
                $frame[8][7] = chr($v);
            else
                $frame[8][6 - $i] = chr($v);

            $format = $format >> 1;
        }

        return $blacks;
    }


    /**
     * @param $x
     * @param $y
     *
     * @return int
     */
    public function mask0($x, $y)
    {
        return ($x + $y) & 1;
    }

    /**
     * @param $x
     * @param $y
     *
     * @return int
     */
    public function mask1($x, $y)
    {
        return ($y & 1);
    }

    /**
     * @param $x
     * @param $y
     *
     * @return int
     */
    public function mask2($x, $y)
    {
        return ($x % 3);
    }

    /**
     * @param $x
     * @param $y
     *
     * @return int
     */
    public function mask3($x, $y)
    {
        return ($x + $y) % 3;
    }

    /**
     * @param $x
     * @param $y
     *
     * @return int
     */
    public function mask4($x, $y)
    {
        return (((int)($y / 2)) + ((int)($x / 3))) & 1;
    }

    /**
     * @param $x
     * @param $y
     *
     * @return int
     */
    public function mask5($x, $y)
    {
        return (($x * $y) & 1) + ($x * $y) % 3;
    }

    /**
     * @param $x
     * @param $y
     *
     * @return int
     */
    public function mask6($x, $y)
    {
        return ((($x * $y) & 1) + ($x * $y) % 3) & 1;
    }

    /**
     * @param $x
     * @param $y
     *
     * @return int
     */
    public function mask7($x, $y)
    {
        return ((($x * $y) % 3) + (($x + $y) & 1)) & 1;
    }


    /**
     * @param $maskNo
     * @param $width
     * @param $frame
     *
     * @return array
     */
    private function generateMaskNo($maskNo, $width, $frame)
    {
        $bitMask = array_fill(0, $width, array_fill(0, $width, 0));

        for ($y = 0; $y < $width; $y++) {
            for ($x = 0; $x < $width; $x++) {
                if (ord($frame[$y][$x]) & 0x80) {
                    $bitMask[$y][$x] = 0;
                } else {
                    $maskFunc = call_user_func(array($this, 'mask' . $maskNo), $x, $y);
                    $bitMask[$y][$x] = ($maskFunc == 0) ? 1 : 0;
                }
            }
        }

        return $bitMask;
    }


    /**
     * @param $bitFrame
     *
     * @return string
     */
    public static function serial($bitFrame)
    {
        $codeArr = [];

        foreach ($bitFrame as $line) {
            $codeArr[] = join('', $line);
        }

        return gzcompress(join("\n", $codeArr), 9);
    }


    /**
     * @param $code
     *
     * @return array
     */
    public static function unserial($code)
    {
        $codeArr = [];

        $codeLines = explode("\n", gzuncompress($code));
        foreach ($codeLines as $line) {
            $codeArr[] = str_split($line);
        }

        return $codeArr;
    }


    /**
     * @param $maskNo
     * @param $width
     * @param $s
     * @param $d
     * @param bool $maskGenOnly
     *
     * @return int|null
     */
    public function makeMaskNo($maskNo, $width, $s, &$d, $maskGenOnly = false)
    {
        $b = 0;

        $cache_dir = Tools::getCacheDir();

        $fileName = $cache_dir . 'mask_' . $maskNo . DIRECTORY_SEPARATOR . 'mask_' . $width . '_' . $maskNo . '.dat';

        if (Enum::QR_USE_CACHE) {
            if (file_exists($fileName))
                $bitMask = static::unserial(file_get_contents($fileName));
            else {
                $bitMask = $this->generateMaskNo($maskNo, $width, $s, $d);
                if (!file_exists($cache_dir . 'mask_' . $maskNo))
                    mkdir($cache_dir . 'mask_' . $maskNo);
                file_put_contents($fileName, static::serial($bitMask));
            }
        } else
            $bitMask = $this->generateMaskNo($maskNo, $width, $s, $d);

        if ($maskGenOnly)
            return null;

        $d = $s;

        for ($y = 0; $y < $width; $y++) {
            for ($x = 0; $x < $width; $x++) {
                if ($bitMask[$y][$x] == 1)
                    $d[$y][$x] = chr(ord($s[$y][$x]) ^ (int)$bitMask[$y][$x]);

                $b += (int)(ord($d[$y][$x]) & 1);
            }
        }

        return $b;
    }


    /**
     * @param $width
     * @param $frame
     * @param $maskNo
     * @param $level
     *
     * @return array
     */
    public function makeMask($width, $frame, $maskNo, $level)
    {
        $masked = array_fill(0, $width, str_repeat("\0", $width));
        $this->makeMaskNo($maskNo, $width, $frame, $masked);
        $this->writeFormatInformation($width, $masked, $maskNo, $level);

        return $masked;
    }


    /**
     * @param $length
     *
     * @return int
     */
    public function calcN1N3($length)
    {
        $demerit = 0;

        for ($i = 0; $i < $length; $i++) {

            if ($this->runLength[$i] >= 5) {
                $demerit += (Enum::N1 + ($this->runLength[$i] - 5));
            }
            if ($i & 1) {
                if (($i >= 3) && ($i < ($length - 2)) && ($this->runLength[$i] % 3 == 0)) {
                    $fact = (int)($this->runLength[$i] / 3);
                    if (($this->runLength[$i - 2] == $fact) &&
                        ($this->runLength[$i - 1] == $fact) &&
                        ($this->runLength[$i + 1] == $fact) &&
                        ($this->runLength[$i + 2] == $fact)
                    ) {
                        if (($this->runLength[$i - 3] < 0) || ($this->runLength[$i - 3] >= (4 * $fact))) {
                            $demerit += Enum::N3;
                        } else if ((($i + 3) >= $length) || ($this->runLength[$i + 3] >= (4 * $fact))) {
                            $demerit += Enum::N3;
                        }
                    }
                }
            }
        }
        return $demerit;
    }


    /**
     * @param $width
     * @param $frame
     *
     * @return int
     */
    public function evaluateSymbol($width, $frame)
    {
        $demerit = 0;
        $frameYM = [];

        for ($y = 0; $y < $width; $y++) {
            $head = 0;
            $this->runLength[0] = 1;

            $frameY = $frame[$y];

            if ($y > 0)
                $frameYM = $frame[$y - 1];

            for ($x = 0; $x < $width; $x++) {
                if (($x > 0) && ($y > 0)) {
                    $b22 = ord($frameY[$x]) & ord($frameY[$x - 1]) & ord($frameYM[$x]) & ord($frameYM[$x - 1]);
                    $w22 = ord($frameY[$x]) | ord($frameY[$x - 1]) | ord($frameYM[$x]) | ord($frameYM[$x - 1]);

                    if (($b22 | ($w22 ^ 1)) & 1) {
                        $demerit += Enum::N2;
                    }
                }
                if (($x == 0) && (ord($frameY[$x]) & 1)) {
                    $this->runLength[0] = -1;
                    $head = 1;
                    $this->runLength[$head] = 1;
                } else if ($x > 0) {
                    if ((ord($frameY[$x]) ^ ord($frameY[$x - 1])) & 1) {
                        $head++;
                        $this->runLength[$head] = 1;
                    } else
                        $this->runLength[$head]++;
                }
            }

            $demerit += $this->calcN1N3($head + 1);
        }

        for ($x = 0; $x < $width; $x++) {
            $head = 0;
            $this->runLength[0] = 1;

            for ($y = 0; $y < $width; $y++) {
                if ($y == 0 && (ord($frame[$y][$x]) & 1)) {
                    $this->runLength[0] = -1;
                    $head = 1;
                    $this->runLength[$head] = 1;
                } else if ($y > 0) {
                    if ((ord($frame[$y][$x]) ^ ord($frame[$y - 1][$x])) & 1) {
                        $head++;
                        $this->runLength[$head] = 1;
                    } else
                        $this->runLength[$head]++;
                }
            }

            $demerit += $this->calcN1N3($head + 1);
        }

        return $demerit;
    }


    /**
     * @param $width
     * @param $frame
     * @param $level
     *
     * @return array
     */
    public function mask($width, $frame, $level)
    {
        $minDemerit = PHP_INT_MAX;

        $checked_masks = array(0, 1, 2, 3, 4, 5, 6, 7);

        if (Enum::QR_FIND_FROM_RANDOM !== false) {

            $howManuOut = 8 - (Enum::QR_FIND_FROM_RANDOM % 9);
            for ($i = 0; $i < $howManuOut; $i++) {
                $remPos = rand(0, count($checked_masks) - 1);
                unset($checked_masks[$remPos]);
                $checked_masks = array_values($checked_masks);
            }
        }

        $bestMask = $frame;

        foreach ($checked_masks as $i) {
            $mask = array_fill(0, $width, str_repeat("\0", $width));

            $blacks = $this->makeMaskNo($i, $width, $frame, $mask);
            $blacks += $this->writeFormatInformation($width, $mask, $i, $level);
            $blacks = (int)(100 * $blacks / ($width * $width));
            $demerit = (int)((int)(abs($blacks - 50) / 5) * Enum::N4);
            $demerit += $this->evaluateSymbol($width, $mask);

            if ($demerit < $minDemerit) {
                $minDemerit = $demerit;
                $bestMask = $mask;
            }
        }

        return $bestMask;
    }
}
