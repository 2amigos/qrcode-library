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

use BaconQrCode\Renderer\Image\EpsImageBackEnd;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;
use Da\QrCode\Contracts\QrCodeInterface;

class EpsWriter extends AbstractWriter
{
    /**
     * EpsWriter constructor.
     */
    public function __construct()
    {
        parent::__construct(new EpsImageBackEnd());
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

        return  $writer->writeString(
            $qrCode->getText(),
            $qrCode->getEncoding(),
            $this->convertErrorCorrectionLevel($qrCode->getErrorCorrectionLevel())
        );
    }

    /**
     * @inheritdoc
     */
    public function getContentType(): string
    {
        return 'image/eps';
    }

    /**
     * @param string          $string
     * @param QrCodeInterface $qrCode
     *
     * @return string
     */
    protected function addMargin(string $string, QrCodeInterface $qrCode): string
    {
        $targetSize = $qrCode->getSize() + $qrCode->getMargin() * 2;
        $lines = explode("\n", $string);
        $sourceBlockSize = 0;
        $additionalWhitespace = $qrCode->getSize();
        foreach ($lines as $line) {
            if (preg_match('#\d+ \d+ \d+ \d+ F#i', $line) && strpos(
                $line,
                $qrCode->getSize() . ' ' . $qrCode->getSize() . ' F'
            ) === false) {
                $parts = explode(' ', $line);
                $sourceBlockSize = $parts[2];
                $additionalWhitespace = min($additionalWhitespace, $parts[0]);
            }
        }
        $blockCount = ($qrCode->getSize() - 2 * $additionalWhitespace) / $sourceBlockSize;
        $targetBlockSize = $qrCode->getSize() / $blockCount;
        $newLines =[];
        foreach ($lines as $line) {
            if (strpos($line, 'BoundingBox') !== false) {
                $newLines[] = '%%BoundingBox: 0 0 ' . $targetSize . ' ' . $targetSize;
            } elseif (strpos($line, $qrCode->getSize() . ' ' . $qrCode->getSize() . ' F') !== false) {
                $newLines[] = '0 0 ' . $targetSize . ' ' . $targetSize . ' F';
            } elseif (preg_match('#\d+ \d+ \d+ \d+ + F#i', $line)) {
                $parts = explode(' ', $line);
                $parts[0] = $qrCode->getMargin(
                    ) + $targetBlockSize * ($parts[0] - $additionalWhitespace) / $sourceBlockSize;
                $parts[1] = $qrCode->getMargin(
                    ) + $targetBlockSize * ($parts[1] - $sourceBlockSize - $additionalWhitespace) / $sourceBlockSize;
                $parts[2] = $targetBlockSize;
                $parts[3] = $targetBlockSize;
                $newLines[] = implode(' ', $parts);
            }
        }
        $string = implode("\n", $newLines);

        return $string;
    }
}



