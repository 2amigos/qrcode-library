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

use BaconQrCode\Renderer\Color\Rgb;
use BaconQrCode\Renderer\RendererInterface;
use Da\QrCode\Contracts\QrCodeInterface;
use Da\QrCode\Contracts\WriterInterface;
use ReflectionClass;
use ReflectionException;

abstract class AbstractWriter implements WriterInterface
{
    /**
     * @var RendererInterface
     */
    protected $renderer;

    /**
     * AbstractWriter constructor.
     *
     * @param RendererInterface $renderer
     */
    protected function __construct(RendererInterface $renderer)
    {
        $this->renderer = $renderer;
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
    protected function convertErrorCorrectionLevel($errorCorrectionLevel): string
    {
        $name = strtoupper($errorCorrectionLevel[0]);
        $errorCorrectionLevel = constant('BaconQrCode\Common\ErrorCorrectionLevel::' . $name);

        return $errorCorrectionLevel;
    }
}
