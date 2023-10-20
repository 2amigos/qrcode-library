<?php

/*
 * This file is part of the 2amigos/qrcode-library project.
 *
 * (c) 2amigOS! <http://2am.tech/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\QrCode\Writer;

use BaconQrCode\Common\ErrorCorrectionLevel;
use BaconQrCode\Renderer\Image\ImageBackEndInterface;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use Da\QrCode\Contracts\QrCodeInterface;
use Da\QrCode\Contracts\WriterInterface;
use Da\QrCode\Dto\LogoDto;
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

    /**
     * @param string $logoPath
     * @param int $logoWidth
     * @param bool $scale
     * @return LogoDto
     */
    protected function transformLogo($logoPath, $logoWidth = null, $scale = false)
    {
        $logoImage = imagecreatefromstring(file_get_contents($logoPath));
        $logoSourceWidth = imagesx($logoImage);
        $logoSourceHeight = imagesy($logoImage);

        $logoTargetWidth = $logoWidth ?: $logoSourceWidth;
        $logoTargetHeight = $logoWidth ?: $logoSourceHeight;

        if ($logoTargetWidth !== null && $scale) {
            $scale = $logoTargetWidth / $logoSourceWidth;
            $logoTargetHeight = intval($scale * imagesy($logoImage));
        }

        return LogoDto::create($logoImage, $logoSourceWidth, $logoSourceHeight, $logoTargetWidth, $logoTargetHeight);
    }

    /**
     * @param QrCodeInterface $qrCode
     * @return RendererStyle
     */
    protected function getRendererStyle(QrCodeInterface $qrCode)
    {
        $margin = $qrCode->getWriter() instanceof EpsWriter
            ? $qrCode->getMargin()
            : 0;

        return new RendererStyle(
            $qrCode->getSize(),
            $margin,
            $qrCode->getStyleManager()->buildModule(),
            $qrCode->getStyleManager()->buildEye(),
            $qrCode->getStyleManager()->buildFillColor()
        );
    }

    /**
     * @param QrCodeInterface $qrCode
     * @return ImageRenderer
     */
    protected function buildRenderer(QrCodeInterface $qrCode)
    {
        return new ImageRenderer(
            $this->getRendererStyle($qrCode),
            $this->renderBackEnd
        );
    }
}
