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

use Da\QrCode\Contracts\LabelInterface;
use Da\QrCode\Exception\InvalidPathException;

class Label implements LabelInterface
{
    /**
     * @var string
     */
    protected $text;
    /**
     * @var int
     */
    protected $fontSize;
    /**
     * @var string
     */
    protected $font;
    /**
     * @var string
     */
    protected $alignment;
    protected $margins = [
        't' => 0,
        'r' => 10,
        'b' => 10,
        'l' => 10,
    ];

    /**
     * @param string $text
     * @param string|null $font
     * @param int|null    $fontSize
     * @param string|null $alignment
     * @param array       $margins
     */
    public function __construct(string $text, string $font = null, $fontSize = null, $align = null, array $margins = [])
    {
        $this->text = $text;
        $this->font = $font ?: __DIR__ . '/../resources/fonts/noto_sans.otf';
        $this->fontSize = $fontSize ?: 16;
        $this->alignment = $align ?: LabelInterface::ALIGN_CENTER;
        $this->margins = array_merge($this->margins, $margins);
    }

    public function setFontSize(int $size): LabelInterface
    {
        $this->fontSize = $size;

        return $this;
    }

    /**
     * @throws InvalidPathException
     */
    public function setFont(string $font): LabelInterface
    {
        $path = realpath($font);
        if (!is_file($path)) {
            throw new InvalidPathException(sprintf('Invalid label font path "%s"', $path));
        }
        $this->font = $path;
        return $this;
    }

    public function getFont(): string
    {
        return $this->font;
    }

    public function getText(): string
    {
        return $this->text;
    }

    public function getFontSize(): int
    {
        return $this->fontSize;
    }

    public function getAlignment(): string
    {
        return $this->alignment;
    }

    public function getMargins(): array
    {
        return $this->margins;
    }
}
