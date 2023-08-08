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

use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Da\QrCode\Contracts\QrCodeInterface;
use SimpleXMLElement;

class SvgWriter extends AbstractWriter
{
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
        $fill = $this->buildQrCodeFillColor($qrCode);

        $rendererStyle = new RendererStyle($qrCode->getSize(), $qrCode->getMargin(), null, null, $fill);

        $renderer = new ImageRenderer(
            $rendererStyle,
            $this->renderBackEnd
        );

        $writer = new Writer($renderer);

        $string = $writer->writeString(
            $qrCode->getText(),
            $qrCode->getEncoding(),
            $this->convertErrorCorrectionLevel($qrCode->getErrorCorrectionLevel())
        );

        $svg = new SimpleXMLElement($string);

        return $svg->asXML();
    }

    /**
     * @return string
     */
    public function getContentType(): string
    {
        return 'image/svg+xml';
    }

    /**
     * @param  string $string
     * @param  int    $margin
     * @param  int    $size
     * @return string
     */
    protected function addMargin(string $string, int $margin, int $size): string
    {
        $targetSize = $size + $margin * 2;
        $xml = new SimpleXMLElement($string);

        $xml['width'] = $targetSize;
        $xml['height'] = $targetSize;
        $xml['viewBox'] = '0 0 '.$targetSize.' '.$targetSize;
        $xml->rect['width'] = $targetSize;
        $xml->rect['height'] = $targetSize;
        $additionalWhitespace = $targetSize;

        foreach ($xml->use as $block) {
            $additionalWhitespace = min($additionalWhitespace, (int) $block['x']);
        }
        $sourceBlockSize = (int) $xml->defs->rect['width'];
        $blockCount = ($size - 2 * $additionalWhitespace) / $sourceBlockSize;
        $targetBlockSize = $size / $blockCount;
        $xml->defs->rect['width'] = $targetBlockSize;
        $xml->defs->rect['height'] = $targetBlockSize;

        foreach ($xml->use as $block) {
            $block['x'] = $margin + $targetBlockSize * ($block['x'] - $additionalWhitespace) / $sourceBlockSize;
            $block['y'] = $margin + $targetBlockSize * ($block['y'] - $additionalWhitespace) / $sourceBlockSize;
        }
        return $xml->asXML();
    }
}
