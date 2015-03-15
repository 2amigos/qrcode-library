<?php
/**
 * @copyright Copyright (c) 2013-15 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\lib;

/**
 * Class Enum
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\lib
 */
class Enum
{
    /**
     * Mode types
     */
    const QR_MODE_NULL = -1;
    const QR_MODE_NUM = 0;
    const QR_MODE_AN = 1;
    const QR_MODE_8 = 2;
    const QR_MODE_KANJI = 3;
    const QR_MODE_STRUCTURE = 4;
    /**
     * Levels of error correction
     */
    const QR_ECLEVEL_L = 0;
    const QR_ECLEVEL_M = 1;
    const QR_ECLEVEL_Q = 2;
    const QR_ECLEVEL_H = 3;
    /**
     * Supported output formats
     */
    const QR_FORMAT_TEXT = 0;
    const QR_FORMAT_PNG = 1;
    const QR_FORMAT_JPG = 2;
    const QR_FORMAT_RAW = 3;
    /**
     * Using cache - more disk reads less CPU power, masks and format
     * templates are stored in cache folder
     */
    const QR_USE_CACHE = true;
    /**
     * If true, estimates best mark (spec. default, but very slow; set to
     * false to significant performance boost but (probably) worst quality
     * code
     */
    const QR_FIND_BEST_MASK = true;
    /**
     * If false, checks all masks available, otherwise value tells count of
     * masks need to be checked, mask id are got randomly
     */
    const QR_FIND_FROM_RANDOM = false;
    const QR_DEFAULT_MASK = 2;
    /**
     * Maximum size
     */
    const QR_PNG_MAXIMUM_SIZE = 1024;
    /**
     * Encode types
     */
    const QR_IMAGE_JPG = 0;
    const QR_IMAGE_PNG = 1;
    /**
     * Structure related
     */
    const STRUCTURE_HEADER_BITS = 20;
    const MAX_STRUCTURED_SYMBOLS = 16;
    /**
     * Specs related
     */
    const QRSPEC_VERSION_MAX = 40;
    const QRSPEC_WIDTH_MAX = 177;

    const QRCAP_WIDTH = 0;
    const QRCAP_WORDS = 1;
    const QRCAP_REMINDER = 2;
    const QRCAP_EC = 3;
    /**
     * Mask related
     */
    const N1 = 3;
    const N2 = 3;
    const N3 = 40;
    const N4 = 10;
}

