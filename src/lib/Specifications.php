<?php
/**
 * @copyright Copyright (c) 2013-15 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\lib;

/**
 * Class Specifications
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\lib
 */
class Specifications
{

    /**
     * @var array
     */
    public static $capacity = [
        [0, 0, 0, [0, 0, 0, 0]],
        [21, 26, 0, [7, 10, 13, 17]], // 1
        [25, 44, 7, [10, 16, 22, 28]],
        [29, 70, 7, [15, 26, 36, 44]],
        [33, 100, 7, [20, 36, 52, 64]],
        [37, 134, 7, [26, 48, 72, 88]], // 5
        [41, 172, 7, [36, 64, 96, 112]],
        [45, 196, 0, [40, 72, 108, 130]],
        [49, 242, 0, [48, 88, 132, 156]],
        [53, 292, 0, [60, 110, 160, 192]],
        [57, 346, 0, [72, 130, 192, 224]], //10
        [61, 404, 0, [80, 150, 224, 264]],
        [65, 466, 0, [96, 176, 260, 308]],
        [69, 532, 0, [104, 198, 288, 352]],
        [73, 581, 3, [120, 216, 320, 384]],
        [77, 655, 3, [132, 240, 360, 432]], //15
        [81, 733, 3, [144, 280, 408, 480]],
        [85, 815, 3, [168, 308, 448, 532]],
        [89, 901, 3, [180, 338, 504, 588]],
        [93, 991, 3, [196, 364, 546, 650]],
        [97, 1085, 3, [224, 416, 600, 700]], //20
        [101, 1156, 4, [224, 442, 644, 750]],
        [105, 1258, 4, [252, 476, 690, 816]],
        [109, 1364, 4, [270, 504, 750, 900]],
        [113, 1474, 4, [300, 560, 810, 960]],
        [117, 1588, 4, [312, 588, 870, 1050]], //25
        [121, 1706, 4, [336, 644, 952, 1110]],
        [125, 1828, 4, [360, 700, 1020, 1200]],
        [129, 1921, 3, [390, 728, 1050, 1260]],
        [133, 2051, 3, [420, 784, 1140, 1350]],
        [137, 2185, 3, [450, 812, 1200, 1440]], //30
        [141, 2323, 3, [480, 868, 1290, 1530]],
        [145, 2465, 3, [510, 924, 1350, 1620]],
        [149, 2611, 3, [540, 980, 1440, 1710]],
        [153, 2761, 3, [570, 1036, 1530, 1800]],
        [157, 2876, 0, [570, 1064, 1590, 1890]], //35
        [161, 3034, 0, [600, 1120, 1680, 1980]],
        [165, 3196, 0, [630, 1204, 1770, 2100]],
        [169, 3362, 0, [660, 1260, 1860, 2220]],
        [173, 3532, 0, [720, 1316, 1950, 2310]],
        [177, 3706, 0, [750, 1372, 2040, 2430]] //40
    ];

    /**
     * @var array
     */
    public static $lengthTableBits = [
        [10, 12, 14],
        [9, 11, 13],
        [8, 16, 16],
        [8, 10, 12]
    ];
    /**
     * @var array Table of the error correction code (Reed-Solomon block). See Table 12-16 (pp.30-36], JIS X0510:2004.
     */
    public static $eccTable = [
        [[0, 0], [0, 0], [0, 0], [0, 0]],
        [[1, 0], [1, 0], [1, 0], [1, 0]], // 1
        [[1, 0], [1, 0], [1, 0], [1, 0]],
        [[1, 0], [1, 0], [2, 0], [2, 0]],
        [[1, 0], [2, 0], [2, 0], [4, 0]],
        [[1, 0], [2, 0], [2, 2], [2, 2]], // 5
        [[2, 0], [4, 0], [4, 0], [4, 0]],
        [[2, 0], [4, 0], [2, 4], [4, 1]],
        [[2, 0], [2, 2], [4, 2], [4, 2]],
        [[2, 0], [3, 2], [4, 4], [4, 4]],
        [[2, 2], [4, 1], [6, 2], [6, 2]], //10
        [[4, 0], [1, 4], [4, 4], [3, 8]],
        [[2, 2], [6, 2], [4, 6], [7, 4]],
        [[4, 0], [8, 1], [8, 4], [12, 4]],
        [[3, 1], [4, 5], [11, 5], [11, 5]],
        [[5, 1], [5, 5], [5, 7], [11, 7]], //15
        [[5, 1], [7, 3], [15, 2], [3, 13]],
        [[1, 5], [10, 1], [1, 15], [2, 17]],
        [[5, 1], [9, 4], [17, 1], [2, 19]],
        [[3, 4], [3, 11], [17, 4], [9, 16]],
        [[3, 5], [3, 13], [15, 5], [15, 10]], //20
        [[4, 4], [17, 0], [17, 6], [19, 6]],
        [[2, 7], [17, 0], [7, 16], [34, 0]],
        [[4, 5], [4, 14], [11, 14], [16, 14]],
        [[6, 4], [6, 14], [11, 16], [30, 2]],
        [[8, 4], [8, 13], [7, 22], [22, 13]], //25
        [[10, 2], [19, 4], [28, 6], [33, 4]],
        [[8, 4], [22, 3], [8, 26], [12, 28]],
        [[3, 10], [3, 23], [4, 31], [11, 31]],
        [[7, 7], [21, 7], [1, 37], [19, 26]],
        [[5, 10], [19, 10], [15, 25], [23, 25]], //30
        [[13, 3], [2, 29], [42, 1], [23, 28]],
        [[17, 0], [10, 23], [10, 35], [19, 35]],
        [[17, 1], [14, 21], [29, 19], [11, 46]],
        [[13, 6], [14, 23], [44, 7], [59, 1]],
        [[12, 7], [12, 26], [39, 14], [22, 41]], //35
        [[6, 14], [6, 34], [46, 10], [2, 64]],
        [[17, 4], [29, 14], [49, 10], [24, 46]],
        [[4, 18], [13, 32], [48, 14], [42, 32]],
        [[20, 4], [40, 7], [43, 22], [10, 67]],
        [[19, 6], [18, 31], [34, 34], [20, 61]] //40
    ];
    /**
     * @var array Positions of alignment patterns. This array includes only the second and the third position of the
     * alignment patterns. Rest of them can be calculated from the distance between them.
     * See Table 1 in Appendix E (pp.71) of JIS X0510:2004.
     */
    public static $alignmentPattern = [
        [0, 0],
        [0, 0],
        [18, 0],
        [22, 0],
        [26, 0],
        [30, 0], // 1- 5
        [34, 0],
        [22, 38],
        [24, 42],
        [26, 46],
        [28, 50], // 6-10
        [30, 54],
        [32, 58],
        [34, 62],
        [26, 46],
        [26, 48], //11-15
        [26, 50],
        [30, 54],
        [30, 56],
        [30, 58],
        [34, 62], //16-20
        [28, 50],
        [26, 50],
        [30, 54],
        [28, 54],
        [32, 58], //21-25
        [30, 58],
        [34, 62],
        [26, 50],
        [30, 54],
        [26, 52], //26-30
        [30, 56],
        [34, 60],
        [30, 58],
        [34, 62],
        [30, 54], //31-35
        [24, 50],
        [28, 54],
        [32, 58],
        [26, 54],
        [30, 58], //35-40
    ];
    /**
     * @var array Version information pattern (BCH coded). See Table 1 in Appendix D (pp.68) of JIS X0510:2004.
     * size: [QRSPEC_VERSION_MAX - 6]
     */
    public static $versionPattern = [
        0x07c94,
        0x085bc,
        0x09a99,
        0x0a4d3,
        0x0bbf6,
        0x0c762,
        0x0d847,
        0x0e60d,
        0x0f928,
        0x10b78,
        0x1145d,
        0x12a17,
        0x13532,
        0x149a6,
        0x15683,
        0x168c9,
        0x177ec,
        0x18ec4,
        0x191e1,
        0x1afab,
        0x1b08e,
        0x1cc1a,
        0x1d33f,
        0x1ed75,
        0x1f250,
        0x209d5,
        0x216f0,
        0x228ba,
        0x2379f,
        0x24b0b,
        0x2542e,
        0x26a64,
        0x27541,
        0x28c69
    ];
    /**
     * @var array format information
     */
    public static $formatInfo = [
        [0x77c4, 0x72f3, 0x7daa, 0x789d, 0x662f, 0x6318, 0x6c41, 0x6976],
        [0x5412, 0x5125, 0x5e7c, 0x5b4b, 0x45f9, 0x40ce, 0x4f97, 0x4aa0],
        [0x355f, 0x3068, 0x3f31, 0x3a06, 0x24b4, 0x2183, 0x2eda, 0x2bed],
        [0x1689, 0x13be, 0x1ce7, 0x19d0, 0x0762, 0x0255, 0x0d0c, 0x083b]
    ];
    /**
     * @var array cache of initial frames
     */
    public static $frames = [];

    /**
     * @param $version
     * @param $level
     *
     * @return mixed
     */
    public static function getDataLength($version, $level)
    {
        return static::$capacity[$version][Enum::QRCAP_WORDS] - static::$capacity[$version][Enum::QRCAP_EC][$level];
    }


    /**
     * @param $version
     * @param $level
     *
     * @return mixed
     */
    public static function getECCLength($version, $level)
    {
        return static::$capacity[$version][Enum::QRCAP_EC][$level];
    }


    /**
     * @param $version
     *
     * @return mixed
     */
    public static function getWidth($version)
    {
        return static::$capacity[$version][Enum::QRCAP_WIDTH];
    }


    /**
     * @param $version
     *
     * @return mixed
     */
    public static function getRemainder($version)
    {
        return static::$capacity[$version][Enum::QRCAP_REMINDER];
    }


    /**
     * @param $size
     * @param $level
     *
     * @return int
     */
    public static function getMinimumVersion($size, $level)
    {

        for ($i = 1; $i <= Enum::QRSPEC_VERSION_MAX; $i++) {
            $words = static::$capacity[$i][Enum::QRCAP_WORDS] - static::$capacity[$i][Enum::QRCAP_EC][$level];
            if ($words >= $size)
                return $i;
        }

        return -1;
    }


    /**
     * @param $mode
     * @param $version
     *
     * @return int
     */
    public static function lengthIndicator($mode, $version)
    {
        if ($mode == Enum::QR_MODE_STRUCTURE)
            return 0;

        if ($version <= 9)
            $l = 0;
        else if ($version <= 26)
            $l = 1;
        else
            $l = 2;

        return static::$lengthTableBits[$mode][$l];
    }


    /**
     * @param $mode
     * @param $version
     *
     * @return int
     */
    public static function maximumWords($mode, $version)
    {
        if ($mode == Enum::QR_MODE_STRUCTURE)
            return 3;

        if ($version <= 9)
            $l = 0;
        else if ($version <= 26)
            $l = 1;
        else
            $l = 2;

        $bits = static::$lengthTableBits[$mode][$l];
        $words = (1 << $bits) - 1;

        if ($mode == Enum::QR_MODE_KANJI)
            $words *= 2; // the number of bytes is required

        return $words;
    }

    /**
     * @param $version
     * @param $level
     * @param array $spec
     */
    public static function getEccSpec($version, $level, array &$spec)
    {
        if (count($spec) < 5) {
            $spec = [0, 0, 0, 0, 0];
        }

        $b1 = static::$eccTable[$version][$level][0];
        $b2 = static::$eccTable[$version][$level][1];
        $data = static::getDataLength($version, $level);
        $ecc = static::getECCLength($version, $level);

        if ($b2 == 0) {
            $spec[0] = $b1;
            $spec[1] = (int)($data / $b1);
            $spec[2] = (int)($ecc / $b1);
            $spec[3] = 0;
            $spec[4] = 0;
        } else {
            $spec[0] = $b1;
            $spec[1] = (int)($data / ($b1 + $b2));
            $spec[2] = (int)($ecc / ($b1 + $b2));
            $spec[3] = $b2;
            $spec[4] = $spec[1] + 1;
        }
    }

    /**
     * Put an alignment marker.
     *
     * @param array $frame
     * @param int $ox x coordinate of the pattern
     * @param int $oy y coordinate of the pattern
     */
    public static function putAlignmentMarker(array &$frame, $ox, $oy)
    {
        $finder = [
            "\xa1\xa1\xa1\xa1\xa1",
            "\xa1\xa0\xa0\xa0\xa1",
            "\xa1\xa0\xa1\xa0\xa1",
            "\xa1\xa0\xa0\xa0\xa1",
            "\xa1\xa1\xa1\xa1\xa1"
        ];

        $yStart = $oy - 2;
        $xStart = $ox - 2;

        for ($y = 0; $y < 5; $y++) {
            static::set($frame, $xStart, $yStart + $y, $finder[$y]);
        }
    }


    /**
     * @param $version
     * @param $frame
     * @param $width
     */
    public static function putAlignmentPattern($version, &$frame, $width)
    {
        if ($version < 2)
            return;

        $d = static::$alignmentPattern[$version][1] - static::$alignmentPattern[$version][0];
        if ($d < 0) {
            $w = 2;
        } else {
            $w = (int)(($width - static::$alignmentPattern[$version][0]) / $d + 2);
        }

        if ($w * $w - 3 == 1) {
            $x = static::$alignmentPattern[$version][0];
            $y = static::$alignmentPattern[$version][0];
            static::putAlignmentMarker($frame, $x, $y);
            return;
        }

        $cx = static::$alignmentPattern[$version][0];
        for ($x = 1; $x < $w - 1; $x++) {
            static::putAlignmentMarker($frame, 6, $cx);
            static::putAlignmentMarker($frame, $cx, 6);
            $cx += $d;
        }

        $cy = static::$alignmentPattern[$version][0];
        for ($y = 0; $y < $w - 1; $y++) {
            $cx = static::$alignmentPattern[$version][0];
            for ($x = 0; $x < $w - 1; $x++) {
                static::putAlignmentMarker($frame, $cx, $cy);
                $cx += $d;
            }
            $cy += $d;
        }
    }


    /**
     * @param $version
     *
     * @return int
     */
    public static function getVersionPattern($version)
    {
        if ($version < 7 || $version > Enum::QRSPEC_VERSION_MAX)
            return 0;

        return static::$versionPattern[$version - 7];
    }

    /**
     * @param $mask
     * @param $level
     *
     * @return int
     */
    public static function getFormatInfo($mask, $level)
    {
        if ($mask < 0 || $mask > 7)
            return 0;

        if ($level < 0 || $level > 3)
            return 0;

        return static::$formatInfo[$level][$mask];
    }

    /**
     * Put a finder pattern.
     *
     * @param frame
     * @param width
     * @param ox ,oy upper-left coordinate of the pattern
     */
    public static function putFinderPattern(&$frame, $ox, $oy)
    {
        $finder = [
            "\xc1\xc1\xc1\xc1\xc1\xc1\xc1",
            "\xc1\xc0\xc0\xc0\xc0\xc0\xc1",
            "\xc1\xc0\xc1\xc1\xc1\xc0\xc1",
            "\xc1\xc0\xc1\xc1\xc1\xc0\xc1",
            "\xc1\xc0\xc1\xc1\xc1\xc0\xc1",
            "\xc1\xc0\xc0\xc0\xc0\xc0\xc1",
            "\xc1\xc1\xc1\xc1\xc1\xc1\xc1"
        ];

        for ($y = 0; $y < 7; $y++) {
            static::set($frame, $ox, $oy + $y, $finder[$y]);
        }
    }


    /**
     * @param $version
     *
     * @return array
     */
    public static function createFrame($version)
    {
        $width = static::$capacity[$version][Enum::QRCAP_WIDTH];
        $frameLine = str_repeat("\0", $width);
        $frame = array_fill(0, $width, $frameLine);

        // Finder pattern
        static::putFinderPattern($frame, 0, 0);
        static::putFinderPattern($frame, $width - 7, 0);
        static::putFinderPattern($frame, 0, $width - 7);

        // Separator
        $yOffset = $width - 7;

        for ($y = 0; $y < 7; $y++) {
            $frame[$y][7] = "\xc0";
            $frame[$y][$width - 8] = "\xc0";
            $frame[$yOffset][7] = "\xc0";
            $yOffset++;
        }

        $setPattern = str_repeat("\xc0", 8);

        static::set($frame, 0, 7, $setPattern);
        static::set($frame, $width - 8, 7, $setPattern);
        static::set($frame, 0, $width - 8, $setPattern);

        // Format info
        $setPattern = str_repeat("\x84", 9);
        static::set($frame, 0, 8, $setPattern);
        static::set($frame, $width - 8, 8, $setPattern, 8);

        $yOffset = $width - 8;

        for ($y = 0; $y < 8; $y++, $yOffset++) {
            $frame[$y][8] = "\x84";
            $frame[$yOffset][8] = "\x84";
        }

        // Timing pattern
        for ($i = 1; $i < $width - 15; $i++) {
            $frame[6][7 + $i] = chr(0x90 | ($i & 1));
            $frame[7 + $i][6] = chr(0x90 | ($i & 1));
        }

        // Alignment pattern
        static::putAlignmentPattern($version, $frame, $width);

        // Version information
        if ($version >= 7) {
            $vinf = static::getVersionPattern($version);

            $v = $vinf;

            for ($x = 0; $x < 6; $x++) {
                for ($y = 0; $y < 3; $y++) {
                    $frame[($width - 11) + $y][$x] = chr(0x88 | ($v & 1));
                    $v = $v >> 1;
                }
            }

            $v = $vinf;
            for ($y = 0; $y < 6; $y++) {
                for ($x = 0; $x < 3; $x++) {
                    $frame[$y][$x + ($width - 11)] = chr(0x88 | ($v & 1));
                    $v = $v >> 1;
                }
            }
        }

        // and a little bit...
        $frame[$width - 8][8] = "\x81";

        return $frame;
    }


    /**
     * @param $frame
     * @param bool $binary_mode
     */
    public static function debug($frame, $binary_mode = false)
    {
        if ($binary_mode) {
            foreach ($frame as &$frameLine) {
                $frameLine = join('<span class="m">&nbsp;&nbsp;</span>', explode('0', $frameLine));
                $frameLine = join('&#9608;&#9608;', explode('1', $frameLine));
            }
            ?>
            <style>
                .m {
                    background-color: white;
                }
            </style>
            <pre><code><br/ ><br/ ><br/ >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <?= join("<br/ >&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;", $frame); ?>
                </code></pre><br/ ><br/ ><br/ ><br/ ><br/ ><br/ >
            <?php
        } else {

            foreach ($frame as &$frameLine) {
                $frameLine = join('<span class="m">&nbsp;</span>', explode("\xc0", $frameLine));
                $frameLine = join('<span class="m">&#9618;</span>', explode("\xc1", $frameLine));
                $frameLine = join('<span class="p">&nbsp;</span>', explode("\xa0", $frameLine));
                $frameLine = join('<span class="p">&#9618;</span>', explode("\xa1", $frameLine));
                $frameLine = join('<span class="s">&#9671;</span>', explode("\x84", $frameLine)); //format 0
                $frameLine = join('<span class="s">&#9670;</span>', explode("\x85", $frameLine)); //format 1
                $frameLine = join('<span class="x">&#9762;</span>', explode("\x81", $frameLine)); //special bit
                $frameLine = join('<span class="c">&nbsp;</span>', explode("\x90", $frameLine)); //clock 0
                $frameLine = join('<span class="c">&#9719;</span>', explode("\x91", $frameLine)); //clock 1
                $frameLine = join('<span class="f">&nbsp;</span>', explode("\x88", $frameLine)); //version
                $frameLine = join('<span class="f">&#9618;</span>', explode("\x89", $frameLine)); //version
                $frameLine = join('&#9830;', explode("\x01", $frameLine));
                $frameLine = join('&#8901;', explode("\0", $frameLine));
            }
            ?>
            <style>
                .p {
                    background-color: yellow;
                }

                .m {
                    background-color: #00FF00;
                }

                .s {
                    background-color: #FF0000;
                }

                .c {
                    background-color: aqua;
                }

                .x {
                    background-color: pink;
                }

                .f {
                    background-color: gold;
                }
            </style>
            <?php

            echo "<pre><code>";
            echo join("<br/ >", $frame);
            echo "</code></pre>";
        }
    }


    /**
     * @param $frame
     *
     * @return string
     */
    public static function serial($frame)
    {
        return gzcompress(join("\n", $frame), 9);
    }


    /**
     * @param $code
     *
     * @return array
     */
    public static function unserial($code)
    {
        return explode("\n", gzuncompress($code));
    }


    /**
     * @param $version
     *
     * @return null
     */
    public static function newFrame($version)
    {
        if ($version < 1 || $version > Enum::QRSPEC_VERSION_MAX)
            return null;

        if (!isset(static::$frames[$version])) {
            $cache_dir = Tools::getCacheDir();

            $fileName = $cache_dir . 'frame_' . $version . '.dat';

            if (Enum::QR_USE_CACHE) {
                if (file_exists($fileName)) {
                    static::$frames[$version] = static::unserial(file_get_contents($fileName));
                } else {
                    static::$frames[$version] = static::createFrame($version);
                    file_put_contents($fileName, static::serial(static::$frames[$version]));
                }
            } else {

                static::$frames[$version] = static::createFrame($version);
            }

        }

        if (is_null(static::$frames[$version]))
            return null;

        return static::$frames[$version];
    }


    /**
     * @param $spec
     *
     * @return mixed
     */
    public static function rsBlockNum($spec)
    {
        return $spec[0] + $spec[3];
    }

    /**
     * @param $spec
     *
     * @return mixed
     */
    public static function rsBlockNum1($spec)
    {
        return $spec[0];
    }

    /**
     * @param $spec
     *
     * @return mixed
     */
    public static function rsDataCodes1($spec)
    {
        return $spec[1];
    }

    /**
     * @param $spec
     *
     * @return mixed
     */
    public static function rsEccCodes1($spec)
    {
        return $spec[2];
    }

    /**
     * @param $spec
     *
     * @return mixed
     */
    public static function rsBlockNum2($spec)
    {
        return $spec[3];
    }

    /**
     * @param $spec
     *
     * @return mixed
     */
    public static function rsDataCodes2($spec)
    {
        return $spec[4];
    }

    /**
     * @param $spec
     *
     * @return mixed
     */
    public static function rsEccCodes2($spec)
    {
        return $spec[2];
    }

    /**
     * @param $spec
     *
     * @return mixed
     */
    public static function rsDataLength($spec)
    {
        return ($spec[0] * $spec[1]) + ($spec[3] * $spec[4]);
    }

    /**
     * @param $spec
     *
     * @return mixed
     */
    public static function rsEccLength($spec)
    {
        return ($spec[0] + $spec[3]) * $spec[2];
    }

    /**
     * @param $srctab
     * @param $x
     * @param $y
     * @param $repl
     * @param bool $replLen
     */
    public static function set(&$srctab, $x, $y, $repl, $replLen = false)
    {
        $srctab[$y] = substr_replace(
            $srctab[$y],
            (($replLen !== false) ? substr($repl, 0, $replLen) : $repl),
            $x,
            ($replLen !== false) ? $replLen : strlen($repl)
        );
    }
}