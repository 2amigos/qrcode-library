<?php

namespace Da\QrCode\Component;

use Da\QrCode\Contracts\LabelInterface;
use Da\QrCode\Contracts\QrCodeInterface;
use Da\QrCode\Contracts\WriterInterface;
use Da\QrCode\QrCode;
use yii\base\Component;

/**
 * Class QrCodeComponent
 *
 * @author Antonio Ramirez <hola@2amigos.us>
 * @package Da\QrCode\Component
 *
 * @method QrCode useForegroundColor(integer $red, integer $green, integer $blue)
 * @method QrCode useBackgroundColor(integer $red, integer $green, integer $blue)
 * @method QrCode useEncoding(string $encoding)
 * @method QrCode useWriter(WriterInterface $writer)
 * @method QrCode setLabel(LabelInterface $label)
 * @method string getText()
 * @method integer getSize()
 * @method array getMargin()
 * @method array getForegroundColor()
 * @method array getBackgroundColor()
 * @method string getEncoding()
 * @method string getErrorCorrectionLevel()
 * @method string getLogoPath()
 * @method integer getLogoWidth()
 * @method LabelInterface getLabel()
 * @method string writeString()
 * @method string writeDataUri()
 * @method bool|integer writeFile(string $path)
 * @method string getContentType()
 */
class QrCodeComponent extends Component
{
    /**
     * @var string
     */
    public $text;
    /**
     * @var int
     */
    public $size = 300;
    /**
     * @var int
     */
    public $margin = 10;
    /**
     * @var array
     */
    public $foregroundColor = [
        'r' => 0,
        'g' => 0,
        'b' => 0
    ];
    /**
     * @var array
     */
    public $backgroundColor = [
        'r' => 255,
        'g' => 255,
        'b' => 255
    ];
    /**
     * @var string
     */
    public $encoding = 'UTF-8';
    /**
     * @var string ErrorCorrectionLevelInterface value
     */
    public $errorCorrectionLevel;
    /**
     * @var string
     */
    public $logoPath;
    /**
     * @var int
     */
    public $logoWidth;
    /**
     * @var LabelInterface
     */
    public $label;
    /**
     * @var WriterInterface
     */
    public $writer;

    /**
     * @var QrCodeInterface
     */
    protected $qrCode;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->qrCode = new QrCode($this->text, $this->errorCorrectionLevel, $this->writer);
    }

    /**
     * @inheritdoc
     */
    public function __call($name, $params)
    {
        return call_user_func_array([$this->qrCode, $name], $params);
    }
}
