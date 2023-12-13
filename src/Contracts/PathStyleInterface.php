<?php

namespace Da\QrCode\Contracts;

use BaconQrCode\Renderer\Eye\EyeInterface;
use BaconQrCode\Renderer\Module\ModuleInterface;

interface PathStyleInterface
{
    public const DOTS = 'dots';
    public const SQUARE = 'square';
    public const ROUNDED = 'rounded';

    /**
     * @param string $pathStyle
     * @return void
     */
    public function setPathStyle(string $pathStyle): void;

    /**
     * @return string
     */
    public function getPathStyle(): string;

    /**
     * @param int $ratio
     * @return void
     */
    public function setIntensity(float $intensity): void;

    /**
     * @return float
     */
    public function getIntensity(): float;

    /**
     * @return ModuleInterface
     */
    public function buildModule();

    /**
     * @return EyeInterface
     */
    public function buildEye();
}
