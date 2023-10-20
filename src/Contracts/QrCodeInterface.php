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

interface QrCodeInterface
{
    /**
     * @return string
     */
    public function getText(): ?string;

    /**
     * @return int
     */
    public function getSize(): int;

    /**
     * @return int
     */
    public function getMargin(): int;

    /**
     * @return int[]
     */
    public function getForegroundColor(): array;

    /**
     * @return int[]
     */
    public function getBackgroundColor(): array;

    /**
     * @return array|null
     */
    public function getForegroundEndColor();

    /**
     * @return string
     */
    public function getEncoding(): string;

    /**
     * @return string
     */
    public function getErrorCorrectionLevel(): string;

    /**
     * @return string
     */
    public function getLogoPath(): ?string;

    /**
     * @return int
     */
    public function getLogoWidth(): ?int;

    /**
     * @var
     * @return bool
     */
    public function isScaleLogoHeight(): bool;

    /**
     * @return LabelInterface
     */
    public function getLabel(): ?LabelInterface;
}
