<?php

namespace Da\QrCode;

use BaconQrCode\Renderer\Color\Alpha;
use BaconQrCode\Renderer\Color\ColorInterface;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Eye\ModuleEye;
use BaconQrCode\Renderer\Module\DotsModule;
use BaconQrCode\Renderer\Module\ModuleInterface;
use BaconQrCode\Renderer\Module\RoundnessModule;
use BaconQrCode\Renderer\Module\SquareModule;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\Gradient;
use BaconQrCode\Renderer\RendererStyle\GradientType;
use Da\QrCode\Contracts\ColorsInterface;
use Da\QrCode\Enums\Gradient as GradientEnum;
use Da\QrCode\Contracts\PathStyleInterface;
use Exception;

class StyleManager implements PathStyleInterface, ColorsInterface
{
    /**
     * @var Alpha|Rgb|Gradient
     */
    private $foregroundColor;
    /**
     * @var
     */
    private $foregroundEndColor;
    /**
     * @var Alpha|Rgb
     */
    private $backgroundColor;
    /**
     * @var string
     */
    private $pathStyle;
    /**
     * @var float
     */
    private $styleIntensity;
    /**
     * @var
     */
    private $gradientType;

    /**
     * @param Alpha|Rgb $foregroundColor
     * @param Alpha|Rgb $backgroundColor
     * @param string|null $pathStyle
     * @param float|null $styleIntensity
     * @param string|null $gradientType
     * @throws Exception
     */
    public function __construct(
        $foregroundColor,
        $backgroundColor,
        string $pathStyle = null,
        float $styleIntensity = null,
        $gradientType = null
    ) {
        $this->setForegroundColor($foregroundColor);
        $this->setBackgroundColor($backgroundColor);

        $this->pathStyle = $pathStyle ?: PathStyleInterface::SQUARE;
        $this->styleIntensity = $styleIntensity ?: 1;
        $this->gradientType = $gradientType ?: GradientEnum::GRADIENT_VERTICAL;
    }

    /**
     * @param $color
     * @return void
     * @throws Exception
     */
    public function setForegroundColor($color): void
    {
        if (! $color instanceof ColorInterface) {
            throw new Exception('Invalid type. Variable `color` should be instance of ' . ColorInterface::class);
        }

        $this->foregroundColor = $color;
    }

    public function getForegroundColor()
    {
        return $this->foregroundColor;
    }

    public function getForegroundEndColor()
    {
        return $this->foregroundEndColor;
    }

    /**
     * @param Alpha|Rgb $color
     * @return void
     * @throws \Exception
     */
    public function setForegroundEndColor($color): void
    {
        if (! $color instanceof ColorInterface) {
            throw new Exception('Invalid type. Variable `color` should be instance of ' . ColorInterface::class);
        }

        $this->foregroundEndColor = $color;
    }

    /**
     * @return void
     */
    public function unsetForegroundEndColor(): void
    {
        $this->foregroundEndColor = null;
    }

    public function setBackgroundColor($color): void
    {
        if (! $color instanceof ColorInterface) {
            throw new Exception('Invalid type. Variable `color` should be instance of ' . ColorInterface::class);
        }

        $this->backgroundColor = $color;
    }

    /**
     * @return Alpha|Rgb|Gradient
     */
    public function getBackgroundColor()
    {
        return $this->backgroundColor;
    }

    /**
     * @param string $pathStyle
     * @return void
     */
    public function setPathStyle(string $pathStyle): void
    {
        $this->pathStyle = $pathStyle;
    }

    /**
     * @return string
     */
    public function getPathStyle(): string
    {
        return $this->pathStyle;
    }

    /**
     * @param float $intensity
     * @return void
     */
    public function setIntensity(float $intensity): void
    {
        $this->styleIntensity = $intensity;
    }

    /**
     * @return float
     */
    public function getIntensity(): float
    {
        return $this->styleIntensity;
    }

    /**
     * @param string $type
     * @return void
     */
    public function setGradientType(string $type): void
    {
        $this->gradientType = $type;
    }

    /**
     * @return GradientType
     */
    public function getGradientTye()
    {
        switch ($this->gradientType) {
            case GradientEnum::GRADIENT_DIAGONAL:
                return GradientType::DIAGONAL();
            case GradientEnum::GRADIENT_INVERSE_DIAGONAL:
                return GradientType::INVERSE_DIAGONAL();
            case GradientEnum::GRADIENT_HORIZONTAL:
                return GradientType::HORIZONTAL();
            case GradientEnum::GRADIENT_RADIAL:
                return GradientType::RADIAL();
            default:
                return GradientType::VERTICAL();
        }
    }

    /**
     * @return void
     */
    public function forceUniformRgbColors(): void
    {
        $this->unsetForegroundEndColor();

        $this->foregroundColor = $this->foregroundColor instanceof Alpha
            ? $this->foregroundColor->getBaseColor()
            : $this->foregroundColor;

        $this->backgroundColor = $this->backgroundColor instanceof Alpha
            ? $this->backgroundColor->getBaseColor()
            : $this->backgroundColor;
    }

    /**
     * @param $red
     * @param $green
     * @param $blue
     * @param $alpha
     * @return Alpha|Rgb
     */
    public static function buildColor($red, $green, $blue, $alpha = 100, $applyAlpha = true)
    {
        if ($applyAlpha) {
            return new Alpha($alpha, new Rgb(
                $red,
                $green,
                $blue,
            ));
        }

        return new Rgb($red, $green, $blue);
    }

    /**
     * @return Fill
     */
    public function buildFillColor()
    {
        if (! is_null($this->foregroundEndColor)) {
            return Fill::uniformGradient(
                $this->getBackgroundColor(),
                new Gradient(
                    $this->getForegroundColor(),
                    $this->getForegroundEndColor(),
                    $this->getGradientTye(),
                )
            );
        }

        return Fill::uniformColor(
            $this->getBackgroundColor(),
            $this->getForegroundColor()
        );
    }

    /**
     * @return ModuleInterface
     */
    public function buildModule()
    {
        switch ($this->getPathStyle()) {
            case PathStyleInterface::DOTS:
                return new DotsModule($this->getIntensity());
            case PathStyleInterface::ROUNDED:
                return new RoundnessModule($this->getIntensity());
            default:
                return SquareModule::instance();
        }
    }

    /**
     * @return ModuleEye
     */
    public function buildEye()
    {
        return new ModuleEye($this->buildModule());
    }
}
