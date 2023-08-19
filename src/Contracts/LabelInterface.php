<?php

/*
 * This file is part of the 2amigos/qrcode-library project.
 *
 * (c) 2amigOS! <http://2am.tech/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\QrCode\Contracts;

use Da\QrCode\Exception\InvalidPathException;

interface LabelInterface
{
    public const ALIGN_LEFT = 'left';
    public const ALIGN_CENTER = 'center';
    public const ALIGN_RIGHT = 'right';

    /**
     * Updates the font size and returns a copy of the instance with the new values.
     *
     * @param int $size
     *
     * @return LabelInterface
     */
    public function setFontSize(int $size): LabelInterface;

    /**
     * Sets the font of the label in the QrCode. Returns a copy of the instance with the new values.
     *
     * @param string $path where the font is located.
     *
     * @throws InvalidPathException
     * @return LabelInterface
     */
    public function setFont(string $path): LabelInterface;

    /**
     * @return string the font path.
     */
    public function getFont(): string;

    /**
     * @return string the label text
     */
    public function getText(): string;

    /**
     * @return int the label font size
     */
    public function getFontSize(): int;

    /**
     * @return string the alignment value
     */
    public function getAlignment(): string;

    /**
     * @return array the margins to position the label.
     */
    public function getMargins(): array;
}
