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

use BaconQrCode\Renderer\Color\Alpha;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\RendererStyle\Fill;
use BaconQrCode\Renderer\RendererStyle\Gradient;
use BaconQrCode\Renderer\RendererStyle\GradientType;

interface ColorsInterface
{
    public const GRADIENT_VERTICAL = 'vertical';
    public const GRADIENT_HORIZONTAL = 'horizontal';
    public const GRADIENT_RADIAL = 'radial';
    public const GRADIENT_DIAGONAL = 'diagonal';
    public const GRADIENT_INVERSE_DIAGONAL = 'diagonal_inverse';

    /**
     * @param Alpha|Rgb|Gradient $color
     * @return void
     */
    public function setForegroundColor($color): void;

    /**
     * @return Rgb|Alpha|Gradient
     */
    public function getForegroundColor();

    /**
     * @param $color
     * @return void
     */
    public function setForegroundEndColor($color): void;

    /**
     * @return mixed
     */
    public function getForegroundEndColor();

    /**
     * @param Alpha|Rgb $color
     * @return void
     */
    public function setBackgroundColor($color): void;

    /**
     * @return void
     */
    public function unsetForegroundEndColor(): void;

    /**
     * @return Rgb|Alpha
     */
    public function getBackgroundColor();

    /**
     * @param string $type
     * @return void
     */
    public function setGradientType(string $type): void;

    /**
     * @return GradientType
     */
    public function getGradientType(): GradientType;

    /**
     * @deprecated since 2.0, misspelled alias of {@see getGradientType()}. Will be removed in 3.0.
     * @return GradientType
     */
    public function getGradientTye(): GradientType;

    /**
     * @return Fill
     */
    public function buildFillColor();

    /**
     * @return void
     */
    public function forceUniformRgbColors(): void;
}
