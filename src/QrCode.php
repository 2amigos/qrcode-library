<?php

/*
 * This file is part of the 2amigos/qrcode-library project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\QrCode;

use Da\QrCode\Contracts\ErrorCorrectionLevelInterface;
use Da\QrCode\Contracts\LabelInterface;
use Da\QrCode\Contracts\QrCodeInterface;
use Da\QrCode\Contracts\WriterInterface;
use Da\QrCode\Exception\InvalidPathException;
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
     * @var array
     */
    protected $foregroundColor = [
        'r' => 0,
        'g' => 0,
        'b' => 0
    ];
    /**
     * @var array
     */
    protected $backgroundColor = [
        'r' => 255,
        'g' => 255,
        'b' => 255
    ];
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
     * @var LabelInterface
     */
    protected $label;
    /**
     * @var WriterInterface
     */
    protected $writer;

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
    }

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     *
     * @return $this
     */
    public function setForegroundColor(int $red, int $green, int $blue): self
    {
        $this->foregroundColor = [
            'r' => $red,
            'g' => $green,
            'b' => $blue,
        ];

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
        $this->backgroundColor = [
            'r' => $red,
            'g' => $green,
            'b' => $blue,
        ];

        return $this;
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

        return $this;
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
        return $this->foregroundColor;
    }

    /**
     * @inheritdoc
     */
    public function getBackgroundColor(): array
    {
        return $this->backgroundColor;
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
}
