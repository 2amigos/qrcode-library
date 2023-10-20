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

use BaconQrCode\Encoder\Encoder;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Writer;
use Da\QrCode\Contracts\LabelInterface;
use Da\QrCode\Contracts\QrCodeInterface;
use SimpleXMLElement;

class SvgWriter extends AbstractWriter
{
    private const PRECISION = 3;

    /**
     * SvgWriter constructor.
     */
    public function __construct()
    {
        parent::__construct(new SvgImageBackEnd());
    }

    /**
     * @inheritdoc
     */
    public function writeString(QrCodeInterface $qrCode): string
    {
        $renderer = $this->buildRenderer($qrCode);

        $writer = new Writer($renderer);

        $string = $writer->writeString(
            $qrCode->getText(),
            $qrCode->getEncoding(),
            $this->convertErrorCorrectionLevel($qrCode->getErrorCorrectionLevel())
        );

        $svg = new SimpleXMLElement($string);

        $this->addMargin($svg, $qrCode);

        if ($qrCode->getLogoPath()) {
            $this->addLogo(
                $svg,
                $qrCode,
                $qrCode->getLogoPath(),
                $qrCode->getLogoWidth(),
                $qrCode->isScaleLogoHeight()
            );
        }

        if ($qrCode->getLabel()) {
            $this->addLabel($svg, $qrCode);
        }

        return $svg->asXML();
    }

    /**
     * @param SimpleXMLElement $svg
     * @param QrCodeInterface $qrCode
     * @return void
     */
    protected function addMargin($svg, $qrCode)
    {
        // encode QR to get proper matrix size to scale SVG
        $encoded = Encoder::encode(
            $qrCode->getText(),
            $this->convertErrorCorrectionLevel($qrCode->getErrorCorrectionLevel()),
            $qrCode->getEncoding()
        );

        $matrix = $encoded->getMatrix()->getWidth();
        $margin = $qrCode->getMargin();
        $size = $qrCode->getSize();
        $scale = round(($size - ($margin * 2))  / $matrix, self::PRECISION);

        $svg->g->attributes()->transform = sprintf("translate(%s, %s), scale(%s)", $margin, $margin, $scale);
    }

    /**
     * @param SimpleXMLElement $svg
     * @param QrCodeInterface $qrCode
     * @param $logoPath
     * @param $logoWidth
     * @param $scale
     * @return void
     */
    protected function addLogo($svg, $qrCode, $logoPath, $logoWidth = null, $scale = false)
    {
        $logoContents = $this->transformLogo($logoPath, $logoWidth, $scale);
        $logoBase64 = base64_encode($this->imageToString($logoContents->image()));
        $image = $svg->addChild('image');

        $logoX = $qrCode->getSize() / 2 - $logoContents->targetWidth() / 2;
        $logoY = $qrCode->getSize() / 2 - $logoContents->targetHeight() / 2;
        $ratio = ! $scale
            ? 'xMinYMid slice'
            : false;

        $image->addAttribute('href', sprintf('data:image/png;base64, %s', $logoBase64));
        $image->addAttribute('width', $logoContents->targetWidth());
        $image->addAttribute('height', $logoContents->targetHeight());
        $image->addAttribute('preserveAspectRatio', $ratio);
        $image->addAttribute('x', $logoX);
        $image->addAttribute('y', $logoY);
    }

    /**
     * @param SimpleXMLElement $svg
     * @param QrCodeInterface $qrCode
     * @return void
     */
    protected function addLabel($svg, $qrCode)
    {
        $label = $qrCode->getLabel();

        $labelText = $label->getText();
        $labelFontSize = $label->getFontSize();
        $labelFontPath = $label->getFont();

        $labelMargin = $label->getMargins();
        $labelAlignment = $label->getAlignment();
        $blockSize = $labelFontSize * 0.5;
        $labelBox = imagettfbbox($labelFontSize, 0, $labelFontPath, $labelText);
        $labelBoxWidth = ($labelBox[2] - $labelBox[0]);

        $qrCodeOriginalWidth = $svg->attributes()->width;
        $qrCodeOriginalHeight = $svg->attributes()->height;
        $svg->attributes()->height = $qrCodeOriginalHeight + $blockSize;
        $svg->attributes()->viewBox = sprintf("0 0 %s %s", $qrCodeOriginalWidth, $qrCodeOriginalHeight + $blockSize);
        $svg->rect->attributes()->height = $qrCodeOriginalHeight + $blockSize;

        $labelFontPath = 'data:application/x-font-otf;charset=utf-8;base64,'
            . base64_encode(
                file_get_contents($label->getFont())
            );
        $svg->addChild(
            'style',
            <<<CSS
                @font-face {
                    font-family: svgFont;
                    src: url($labelFontPath);    
                }
            CSS
        );

        $labelY = max(($qrCodeOriginalHeight + $blockSize) - $labelMargin['b'], $qrCodeOriginalHeight + 4);

        switch ($labelAlignment) {
            case LabelInterface::ALIGN_LEFT:
                $labelX = $labelMargin['l'];
                break;
            case LabelInterface::ALIGN_RIGHT:
                $labelX = $qrCodeOriginalWidth - $labelBoxWidth + $labelMargin['r'] * 2;
                break;
            default:
                $labelX = (int)($qrCodeOriginalWidth / 2 - $labelBoxWidth / 2.6);
                break;
        }

        $labelNode = $svg->addChild('text', $labelText);
        $labelNode->addAttribute('x', $labelX);
        $labelNode->addAttribute('y', $labelY);
        $labelNode->addAttribute('style', "font-size: {$labelFontSize}px; font-family: svgFont");
    }

    /**
     * @param resource $image
     *
     * @return string
     */
    protected function imageToString($image): string
    {
        ob_start();
        imagepng($image);

        return ob_get_clean();
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return 'image/svg+xml';
    }
}
