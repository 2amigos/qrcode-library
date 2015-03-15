<?php
/**
 * @copyright Copyright (c) 2013-15 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\lib;

use Yii;
use yii\base\Object;

/**
 * Class Encode
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\lib
 */
class Encode extends Object
{
    /**
     * @var bool whether to be case senstive or not
     */
    public $caseSensitive = true;
    /**
     * @var bool whether to use an eight bit encoding
     */
    public $eightBit = false;
    /**
     * @var int version number
     */
    public $version = 0;
    /**
     * @var int size of the image
     */
    public $size = 3;
    /**
     * @var int the marging
     */
    public $margin = 4;
    /**
     * @var int
     */
    public $structured = 0; // not supported yet
    /**
     * @var int the error correction level. Possible values:
     *
     * - [[Enum::QR_ECLEVEL_L]] : Low 7% of codewords can be restored.
     * - [[Enum::QR_ECLEVEL_M]] : Medium 15% of codewords can be restored
     * - [[Enum::QR_ECLEVEL_Q]] : Quartile 25% of codewords can be restored
     * - [[Enum::QR_ECLEVEL_H]] : High 30% of codewords can be restored
     *
     * @see http://en.wikipedia.org/wiki/QR_code
     */
    public $level = Enum::QR_ECLEVEL_L;
    /**
     * @var int the Mode of encoding
     * @see http://en.wikipedia.org/wiki/QR_code
     */
    public $hint = Enum::QR_MODE_8;

    /**
     * @param int $level
     * @param int $size
     * @param int $margin
     *
     * @return Encode
     */
    public static function factory($level = Enum::QR_ECLEVEL_L, $size = 3, $margin = 4)
    {

        switch ($level . '') {
            case 'l':
            case 'L':
                $level = Enum::QR_ECLEVEL_L;
                break;
            case 'm':
            case 'M':
                $level = Enum::QR_ECLEVEL_M;
                break;
            case 'q':
            case 'Q':
                $level = Enum::QR_ECLEVEL_Q;
                break;
            case 'h':
            case 'H':
                $level = Enum::QR_ECLEVEL_H;
                break;
            case '0':
            case '1':
            case '2':
            case '3':
                // we keep level as it is
                break;
        }

        return new Encode(['size' => $size, 'margin' => $margin, 'level' => $level]);
    }

    /**
     * @param $text
     * @param bool $outfile
     * @param bool $raw
     *
     * @return array|int
     */
    public function encode($text, $outfile = false, $raw = false)
    {
        $code = new Code();

        if ($this->eightBit) {
            $code->encodeString8bit($text, $this->version, $this->level);
        } else {
            $code->encodeString($text, $this->version, $this->level, $this->hint, $this->caseSensitive);
        }

        Tools::markTime('after_encode');

        return ($outfile !== false)
            ? file_put_contents($outfile, join("\n", Tools::binarize($code->data)))
            : ($raw ? $code->data : Tools::binarize($code->data));
    }

    /**
     * @param $text
     * @param bool $outfile
     * @param bool $saveAndPrint
     * @param RgbColor $fgColor
     * @param RgbColor $bgColor
     */
    public function encodePNG(
        $text,
        $outfile = false,
        $saveAndPrint = false,
        RgbColor $fgColor = null,
        RgbColor $bgColor = null
    ) {
        $this->image($text, $outfile, $saveAndPrint, Enum::QR_IMAGE_PNG, $fgColor, $bgColor);
    }

    /**
     * @param $text
     * @param bool $outfile
     * @param bool $saveAndPrint
     * @param RgbColor $fgColor
     * @param RgbColor $bgColor
     */
    public function encodeJPG(
        $text,
        $outfile = false,
        $saveAndPrint = false,
        RgbColor $fgColor = null,
        RgbColor $bgColor = null
    ) {
        $this->image($text, $outfile, $saveAndPrint, Enum::QR_IMAGE_JPG, $fgColor, $bgColor);
    }

    /**
     * @param $text
     * @param bool $outfile
     * @param bool $saveAndPrint
     * @param int $imageType
     * @param RgbColor $fgColor
     * @param RgbColor $bgColor
     */
    protected function image(
        $text,
        $outfile = false,
        $saveAndPrint = false,
        $imageType = Enum::QR_IMAGE_PNG,
        RgbColor $fgColor = null,
        RgbColor $bgColor = null
    ) {
        try {

            ob_start();
            $tab = $this->encode($text);
            $err = ob_get_contents();
            ob_end_clean();

            if ($err != '') {
                Yii::error($err);
            }

            $maxSize = (int)(Enum::QR_PNG_MAXIMUM_SIZE / (count($tab) + 2 * $this->margin));

            $method = ($imageType !== Enum::QR_IMAGE_JPG || $imageType !== Enum::QR_IMAGE_PNG)
                ? 'png'
                : ($imageType === Enum::QR_IMAGE_JPG ? 'jpg' : 'png');


            Image::$method(
                $tab,
                $outfile,
                min(max(1, $this->size), $maxSize),
                $this->margin,
                $saveAndPrint,
                $fgColor,
                $bgColor
            );


        } catch (\Exception $e) {
            Yii::error($e->getMessage());
        }
    }
}
