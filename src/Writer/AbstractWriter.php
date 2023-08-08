<?php

/*
 * This file is part of the 2amigos/qrcode-library project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\QrCode\Writer;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\Image\ImageBackEndInterface;
use BaconQrCode\Renderer\RendererStyle\Fill;
use Da\QrCode\Contracts\QrCodeInterface;
use Da\QrCode\Contracts\WriterInterface;
use ReflectionClass;
use ReflectionException;

abstract class AbstractWriter implements WriterInterface
{
    /**
     * @var ImageBackEndInterface
     */
    protected $renderBackEnd;

    /**
     * AbstractWriter constructor.
     *
     * @param ImageBackEndInterface $renderBackEnd
     */
    protected function __construct(ImageBackEndInterface $renderBackEnd)
    {
        $this->renderBackEnd = $renderBackEnd;
    }

    /**
     * @param QrCodeInterface $qrCode
     * @return Fill
     */
    protected function buildQrCodeFillColor(QrCodeInterface $qrCode): Fill
    {
        $background = $qrCode->getBackgroundColor();
        $foreground = $qrCode->getForegroundColor();

        return Fill::uniformColor(
            new Rgb($background['r'], $background['g'], $background['b']),
            new Rgb($foreground['r'], $foreground['g'], $foreground['b']),
        );
    }

    /**
     * @inheritdoc
     */
    public function writeDataUri(QrCodeInterface $qrCode): string
    {
        return 'data:' . $this->getContentType() . ';base64,' . base64_encode($this->writeString($qrCode));
    }

    /**
     * @inheritdoc
     */
    public function writeFile(QrCodeInterface $qrCode, $path)
    {
        return file_put_contents($path, $this->writeString($qrCode));
    }

    /**
     * @inheritdoc
     * @throws ReflectionException
     */
    public function getName(): string
    {
        return strtolower(str_replace('Writer', '', (new ReflectionClass($this))->getShortName()));
    }

    /**
     * @param array $color
     *
     * @return Rgb
     */
    protected function convertColor(array $color): Rgb
    {
        return new Rgb($color['r'], $color['g'], $color['b']);
    }

    /**
     * @param string $errorCorrectionLevel
     *
     * @return string
     */
    protected function convertErrorCorrectionLevel($errorCorrectionLevel): ?ErrorCorrectionLevel
    {
        $name = strtoupper($errorCorrectionLevel[0]);
        $errorCorrectionLevel = ErrorCorrectionLevel::$name();

        return $errorCorrectionLevel;
    }
}
