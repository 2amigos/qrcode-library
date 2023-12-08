<?php

namespace Da\QrCode\Component;

use Da\QrCode\Factory\LaravelQrCodeFactory;
use Illuminate\View\Component;
use Exception;

class QrCodeBladeComponent extends Component
{
    /**
     * @var string|array
     */
    public $content;
    /**
     * @var string|null
     */
    public $format = null;
    /**
     * @var array|null
     */
    public $foreground = null;
    /**
     * @var array|null
     */
    public $background = null;
    /**
     * @var string|null
     */
    public $pathStyle = null;
    /**
     * @var float|null
     */
    public $intensity = null;
    /**
     * @var array|null
     */
    public $foreground2 = null;
    /**
     * @var int|null
     */
    public $margin = null;
    /**
     * @var int|null
     */
    public $size = null;
    /**
     * @var string|null
     */
    public $logoPath = null;
    /**
     * @var int|null
     */
    public $logoSize = null;
    /**
     * @var bool|null
     */
    public $scaleLogoHeight = null;
    /**
     * @var string|null
     */
    public $gradientType = null;

    /**
     * @var string|null
     */
    public $label = null;

    /**
     * @var string|null
     */
    public $fontPath = null;

    /**
     * @var int|null
     */
    public $fontSize = null;

    /**
     * @var string|null
     */
    public $alignment = null;

    /**
     * @param $content
     * @param string|null $format
     * @param array|null $foreground
     * @param array|null $background
     * @param string|null $pathStyle
     * @param float|null $intensity
     * @param array|null $foreground2
     * @param int|null $margin
     * @param int|null $size
     * @param string|null $logoPath
     * @param string|null $logoSize
     * @param bool|null $scaleLogoHeight
     * @param string|null $gradientType
     * @param string|null $label
     * @param string|null $font
     * @param int|null $fontSize
     * @param string|null $fontAlign
     */
    public function __construct(
        $content,
        ?string $format = null,
        ?array $foreground = null,
        ?array $background = null,
        ?string $pathStyle = null,
        ?float $intensity = null,
        ?array $foreground2 = null,
        ?int $margin = null,
        ?int $size = null,
        ?string $logoPath = null,
        ?string $logoSize = null,
        ?bool $scaleLogoHeight = null,
        ?string $gradientType = null,
        ?string $label = null,
        ?string $font = null,
        ?int $fontSize = null,
        ?string $fontAlign = null
    ) {
        $this->content = $content;
        $this->format = $format;
        $this->foreground = $foreground;
        $this->background = $background;
        $this->foreground2 = $foreground2;
        $this->pathStyle = $pathStyle;
        $this->intensity = $intensity;
        $this->margin = $margin;
        $this->size = $size;
        $this->logoPath = $logoPath;
        $this->logoSize = $logoSize;
        $this->scaleLogoHeight = $scaleLogoHeight;
        $this->gradientType = $gradientType;
        $this->label = $label;
        $this->fontPath = $font;
        $this->fontSize = $fontSize;
        $this->alignment = $fontAlign;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function buildQrCodeUri(): string
    {
        $qrCode = LaravelQrCodeFactory::make(
            $this->content,
            $this->format,
            $this->foreground,
            $this->background,
            $this->pathStyle,
            $this->intensity,
            $this->foreground2,
            $this->margin,
            $this->size,
            $this->logoPath,
            $this->logoSize,
            $this->scaleLogoHeight,
            $this->gradientType,
            $this->label,
            $this->fontPath,
            $this->fontSize,
            $this->alignment
        );

        return $qrCode->writeDataUri();
    }

    public function render()
    {
        return view('2am-qrcode::components.qrcode');
    }
}
