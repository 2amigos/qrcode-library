<?php
/**
 * @copyright Copyright (c) 2013-15 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode;

use dosamigos\qrcode\lib\Encode;
use dosamigos\qrcode\lib\Enum;
use yii\base\InvalidParamException;

/**
 * Class QrCode creates a QrCode image using the ported to PHP Dominik Dzienia library to render QrCodes
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode
 */
class QrCode
{

    /**
     * Creates a qr code in png format of the text provided
     * @param string $text the text to encode
     * @param string|bool $outfile the full file path to save as an image. If false will render an image.
     * @param int $level the error correction level. Defaults to [[\dosamigos\qr\lib\Enum::QR_ECLEVEL_L]] (low)
     * @param int $size the size of the image. Defaults to 3.
     * @param int $margin the margin of the image. Defaults to 4.
     * @param bool $saveAndPrint whether to also render the image even if we save it on a file.
     */
    public static function png(
        $text,
        $outfile = false,
        $level = Enum::QR_ECLEVEL_L,
        $size = 3,
        $margin = 4,
        $saveAndPrint = false
    ) {
        static::encode($text, $outfile, $level, $size, $margin, $saveAndPrint, Enum::QR_FORMAT_PNG);
    }


    /**
     * Creates a qr code in jpg format of the text provided
     * @param string $text the text to encode
     * @param string|bool $outfile the full file path to save as an image. If false will render an image.
     * @param int $level the error correction level. Defaults to [[\dosamigos\qr\lib\Enum::QR_ECLEVEL_L]] (low)
     * @param int $size the size of the image. Defaults to 3.
     * @param int $margin the margin of the image. Defaults to 4.
     * @param bool $saveAndPrint whether to also render the image even if we save it on a file.
     */
    public static function jpg(
        $text,
        $outfile = false,
        $level = Enum::QR_ECLEVEL_L,
        $size = 3,
        $margin = 4,
        $saveAndPrint = false
    ) {
        static::encode($text, $outfile, $level, $size, $margin, $saveAndPrint, Enum::QR_FORMAT_JPG);
    }


    /**
     * Encodes a text on png format
     * @param string $text the text to be encoded
     * @param string|bool $outfile the full file path to save as an image. If false will render an image.
     * @param int $level the error correction level. Defaults to [[\dosamigos\qr\lib\Enum::QR_ECLEVEL_L]] (low)
     * @param int $size the size of the image. Defaults to 3.
     * @param int $margin the margin of the image. Defaults to 4.
     * @return array|int
     */
    public static function text($text, $outfile = false, $level = Enum::QR_ECLEVEL_L, $size = 3, $margin = 4)
    {
        return static::encode($text, $outfile, $level, $size, $margin, false, Enum::QR_FORMAT_TEXT);
    }


    /**
     * Encodes a string in raw format
     * @param string $text the text to be encoded
     * @param string|bool $outfile the full file path to save as an image. If false will render an image.
     * @param int $level the error correction level. Defaults to [[\dosamigos\qr\lib\Enum::QR_ECLEVEL_L]] (low)
     * @param int $size the size of the image. Defaults to 3.
     * @param int $margin the margin of the image. Defaults to 4.
     * @return array|int
     */
    public static function raw($text, $outfile = false, $level = Enum::QR_ECLEVEL_L, $size = 3, $margin = 4)
    {
        return static::encode($text, $outfile, $level, $size, $margin, false, Enum::QR_FORMAT_RAW);
    }

    /**
     * Creates a Qr Code in Png, Jpg, Raw or Text format
     *
     * @param string $text the text to be encoded
     * @param string|bool $outfile the full file path to save as an image. If false will render an image.
     * @param int $level the error correction level. Defaults to [[\dosamigos\qr\lib\Enum::QR_ECLEVEL_L]] (low)
     * @param int $size the size of the image. Defaults to 3.
     * @param int $margin the margin of the image. Defaults to 4.
     * @param bool $saveAndPrint
     * @param int $type
     * @return array|int
     * @throws \yii\base\InvalidParamException
     */
    public static function encode(
        $text,
        $outfile = false,
        $level = Enum::QR_ECLEVEL_L,
        $size = 3,
        $margin = 4,
        $saveAndPrint = false,
        $type = Enum::QR_FORMAT_PNG
    ) {
        $enc = Encode::factory($level, $size, $margin);

        switch ($type) {
            case Enum::QR_FORMAT_PNG:
                $enc->encodePNG($text, $outfile, $saveAndPrint);
                break;
            case Enum::QR_FORMAT_JPG:
                $enc->encodeJPG($text, $outfile, $saveAndPrint);
                break;
            case Enum::QR_FORMAT_RAW:
                return $enc->encode($text, $outfile, true);
            case Enum::QR_FORMAT_TEXT:
                return $enc->encode($text, $outfile);
            default:
                throw new InvalidParamException("Unknown format $type");
        }
    }
}