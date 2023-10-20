<?php

/*
 * This file is part of the 2amigos/qrcode-library project.
 *
 * (c) 2amigOS! <http://2am.tech/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\QrCode;

use BaconQrCode\Renderer\Color\Alpha;
use Da\QrCode\Contracts\ErrorCorrectionLevelInterface;
use Da\QrCode\Contracts\LabelInterface;
use Da\QrCode\Contracts\QrCodeInterface;
use Da\QrCode\Contracts\WriterInterface;
use Da\QrCode\Exception\InvalidPathException;
use Da\QrCode\Writer\EpsWriter;
use Da\QrCode\Writer\PngWriter;

class QrCode implements QrCodeInterface
{
    /**
     * @var string
     */
    protected $text;
    /**
     * @var int
     */
    protected $size = 300;
    /**
     * @var int
     */
    protected $margin = 10;
    /**
     * @var string
     */
    protected $encoding = 'UTF-8';
    /**
     * @var string ErrorCorrectionLevelInterface value
     */
    protected $errorCorrectionLevel;
    /**
     * @var string
     */
    protected $logoPath;
    /**
     * @var int
     */
    protected $logoWidth;
    /**
     * @var bool
     */
    protected $scaleLogoHeight = false;
    /**
     * @var LabelInterface
     */
    protected $label;
    /**
     * @var WriterInterface
     */
    protected $writer;

    /**
     * @var StyleManager
     */
    protected $styleManager;

    /**
     * QrCode constructor.
     *
     * @param string|null $text
     * @param string|null $errorCorrectionLevel
     * @param WriterInterface|null $writer
     */
    public function __construct(string $text, string $errorCorrectionLevel = null, WriterInterface $writer = null)
    {
        $this->text = $text;
        $this->errorCorrectionLevel = $errorCorrectionLevel ?: ErrorCorrectionLevelInterface::LOW;
        $this->writer = $writer ?: new PngWriter();

        $canApplyAlpha = ! $this->writer instanceof EpsWriter;

        $this->styleManager = new StyleManager(
            StyleManager::buildColor(0, 0, 0, 100, $canApplyAlpha),
            StyleManager::buildColor(255, 255, 255, 100, $canApplyAlpha)
        );
    }

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     *
     * @return $this
     */
    public function setForegroundColor(int $red, int $green, int $blue, int $alpha = 100): self
    {
        $color = StyleManager::buildColor(
            $red,
            $green,
            $blue,
            $alpha,
            ! $this->getWriter() instanceof EpsWriter
        );

        $this->styleManager->setForegroundColor($color);

        return $this;
    }

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     *
     * @return $this
     */
    public function setForegroundEndColor(int $red, int $green, int $blue, int $alpha = 100): self
    {
        $color = StyleManager::buildColor(
            $red,
            $green,
            $blue,
            $alpha,
            !$this->getWriter() instanceof EpsWriter
        );

        $this->styleManager->setForegroundEndColor($color);

        return $this;
    }

    /**
     * @return $this
     */
    public function unsetForegroundEndColor(): self
    {
        $this->styleManager->unsetForegroundEndColor();

        return $this;
    }

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     *
     * @return $this
     */
    public function setBackgroundColor(int $red, int $green, int $blue): self
    {
        $color = StyleManager::buildColor(
            $red,
            $green,
            $blue,
            100,
            ! $this->getWriter() instanceof EpsWriter
        );

        $this->styleManager->setBackgroundColor($color);

        return $this;
    }

    /**
     * @param string $style
     * @return $this
     */
    public function setPathStyle(string $style): self
    {
        $this->styleManager->setPathStyle($style);

        return $this;
    }

    /**
     * @param float $intensity
     * @return $this
     */
    public function setPathIntensity(float $intensity): self
    {
        $this->styleManager->setIntensity($intensity);

        return $this;
    }

    /**
     * @return StyleManager
     */
    public function getStyleManager()
    {
        return $this->styleManager;
    }

    /**
     * @return float
     */
    public function getPathIntensity(): float
    {
        return $this->styleManager->getIntensity();
    }

    /**
     * @return string
     */
    public function getGradientType(): string
    {
        return $this
            ->styleManager
            ->getGradientTye();
    }

    /**
     * @param string $path
     *
     * @return $this
     * @throws InvalidPathException
     */
    public function setLogo(string $path): self
    {
        $logo = realpath($path);
        if (!is_file($logo)) {
            throw new InvalidPathException(sprintf('Invalid logo path: "%s"', $logo));
        }
        $this->logoPath = $logo;

        return $this;
    }

    /**
     * @param string $encoding
     *
     * @return $this
     */
    public function setEncoding(string $encoding): self
    {
        $this->encoding = $encoding;

        return $this;
    }

    /**
     * @param WriterInterface $writer
     *
     * @return $this
     */
    public function setWriter(WriterInterface $writer): self
    {
        $this->writer = $writer;

        if ($writer instanceof EpsWriter) {
            $this->styleManager->forceUniformRgbColors();
        }

        return $this;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setGradientType(string $type): self
    {
        $this->styleManager->setGradientType($type);

        return $this;
    }

    /**
     * @return WriterInterface
     */
    public function getWriter(): WriterInterface
    {
        return $this->writer;
    }

    /**
     * @param string $errorCorrectionLevel
     *
     * @return $this
     */
    public function setErrorCorrectionLevel(string $errorCorrectionLevel): self
    {
        $this->errorCorrectionLevel = $errorCorrectionLevel;

        return $this;
    }

    /**
     * @param string $text
     *
     * @return $this
     */
    public function setText(string $text): self
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @param int $size
     *
     * @return $this
     */
    public function setSize(int $size): self
    {
        $this->size = $size;

        return $this;
    }

    /**
     * @param int $margin
     *
     * @return $this
     */
    public function setMargin(int $margin): self
    {
        $this->margin = $margin;

        return $this;
    }

    /**
     * @param int $width
     *
     * @return $this
     */
    public function setLogoWidth(int $width): self
    {
        $this->logoWidth = $width;

        return $this;
    }

    /**
     * @param bool $scale
     * @return $this
     */
    public function setScaleLogoHeight(bool $scale): self
    {
        $this->scaleLogoHeight = $scale;

        return $this;
    }

    /**
     * @param LabelInterface|string $label
     *
     * @return $this
     */
    public function setLabel($label): self
    {
        $this->label = $label instanceof LabelInterface ? $label : new Label($label);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getText(): ?string
    {
        return $this->text;
    }

    /**
     * @inheritdoc
     */
    public function getSize(): int
    {
        return $this->size;
    }

    /**
     * @inheritdoc
     */
    public function getMargin(): int
    {
        return $this->margin;
    }

    /**
     * @inheritdoc
     */
    public function getForegroundColor(): array
    {
        $color = $this->styleManager->getForegroundColor();
        $rgb = $color->toRgb();

        return [
            'r' => $rgb->getRed(),
            'g' => $rgb->getGreen(),
            'b' => $rgb->getBlue(),
            'a' => $color instanceof Alpha
                ? $color->getAlpha()
                : 100
        ];
    }

    /**
     * @inheritdoc
     */
    public function getForegroundEndColor()
    {
        $color = $this->styleManager->getForegroundEndColor();

        if (is_null($color)) {
            return null;
        }

        $rgb = $color->toRgb();

        return [
            'r' => $rgb->getRed(),
            'g' => $rgb->getGreen(),
            'b' => $rgb->getBlue(),
            'a' => $color instanceof Alpha
                ? $color->getAlpha()
                : 100
        ];
    }

    /**
     * @inheritdoc
     */
    public function getBackgroundColor(): array
    {
        $color = $this->styleManager->getBackgroundColor();
        $rgb = $color->toRgb();

        return [
            'r' => $rgb->getRed(),
            'g' => $rgb->getGreen(),
            'b' => $rgb->getBlue()
        ];
    }

    /**
     * @inheritdoc
     */
    public function getEncoding(): string
    {
        return $this->encoding;
    }

    /**
     * @inheritdoc
     */
    public function getErrorCorrectionLevel(): string
    {
        return $this->errorCorrectionLevel;
    }

    /**
     * @inheritdoc
     */
    public function getLogoPath(): ?string
    {
        return $this->logoPath;
    }

    /**
     * @inheritdoc
     */
    public function getLogoWidth(): ?int
    {
        return $this->logoWidth;
    }

    /**
     * @inheritdoc
     */
    public function getLabel(): ?LabelInterface
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return $this->writer->getContentType();
    }

    /**
     * @throws Exception\ValidationException
     * @throws Exception\BadMethodCallException
     * @return string
     */
    public function writeString(): string
    {
        return $this->writer->writeString($this);
    }

    /**
     * @return string
     */
    public function writeDataUri(): string
    {
        return $this->writer->writeDataUri($this);
    }

    /**
     * @param string $path
     *
     * @return bool|int
     */
    public function writeFile(string $path)
    {
        return $this->writer->writeFile($this, $path);
    }

    /**
     * @return bool
     */
    public function isScaleLogoHeight(): bool
    {
        return $this->scaleLogoHeight;
    }
}
