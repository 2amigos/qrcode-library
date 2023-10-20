<?php

/*
 * This file is part of the 2amigos/qrcode-library project.
 *
 * (c) 2amigOS! <http://2am.tech/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\QrCode\Traits;

use BaconQrCode\Writer;
use Da\QrCode\Contracts\LabelInterface;
use Da\QrCode\Contracts\QrCodeInterface;
use Da\QrCode\Exception\BadMethodCallException;
use Da\QrCode\Exception\ValidationException;
use Zxing\QrReader;

trait ImageTrait
{
    protected $validate = false;

    /**
     * Whether to validate result or not.
     *
     * @param bool $validate
     *
     * @return $this
     */
    public function validateResult(bool $validate): self
    {
        $this->validate = $validate;

        return $this;
    }

    /**
     * {@inheritdoc}
     * @throws ValidationException
     * @throws BadMethodCallException
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

        $image = imagecreatefromstring($string);
        $image = $this->addMargin(
            $image,
            $qrCode->getMargin(),
            $qrCode->getSize(),
            $qrCode->getForegroundColor(),
            $qrCode->getBackgroundColor()
        );

        if ($qrCode->getLogoPath()) {
            $image = $this->addLogo(
                $image,
                $qrCode->getLogoPath(),
                $qrCode->getLogoWidth(),
                $qrCode->isScaleLogoHeight()
            );
        }

        if ($qrCode->getLabel()) {
            $image = $this->addLabel(
                $image,
                $qrCode->getLabel(),
                $qrCode->getForegroundColor(),
                $qrCode->getBackgroundColor()
            );
        }

        $string = $this->imageToString($image);
        if ($this->validate) {
            $this->validateOutput($string, $qrCode);
        }

        return $string;
    }

    /**
     * @param string $expectedImageString
     * @param QrCodeInterface $qrCode
     * @return void
     * @throws ValidationException
     */
    public function validateOutput(string $expectedImageString, QrCodeInterface $qrCode)
    {
        $reader = new QrReader($expectedImageString, QrReader::SOURCE_TYPE_BLOB);

        if ($reader->text() !== $qrCode->getText()) {
            throw new ValidationException(
                sprintf(
                    'Built-in validation reader read "%s" instead of "%s"' .
                    'Adjust your parameters to increase readability or disable built-in validation.',
                    $reader->text(),
                    $qrCode->getText()
                )
            );
        }
    }

    /**
     * @param resource $sourceImage
     * @param int      $margin
     * @param int      $size
     * @param int[]    $foregroundColor
     * @param int[]    $backgroundColor
     *
     * @return resource
     */
    protected function addMargin($sourceImage, $margin, $size, array $foregroundColor, array $backgroundColor)
    {
        $additionalWhitespace = $this->calculateAdditionalWhiteSpace($sourceImage, $foregroundColor);

        if ($margin === 0) {
            return $sourceImage;
        }

        $targetImage = imagecreatetruecolor($size + $margin * 2, $size + $margin * 2);
        $backgroundColor = imagecolorallocate(
            $targetImage,
            $backgroundColor['r'],
            $backgroundColor['g'],
            $backgroundColor['b']
        );
        imagefill($targetImage, 0, 0, $backgroundColor);
        imagecopyresampled(
            $targetImage,
            $sourceImage,
            $margin,
            $margin,
            $additionalWhitespace,
            $additionalWhitespace,
            $size,
            $size,
            $size - 2 * $additionalWhitespace,
            $size - 2 * $additionalWhitespace
        );

        return $targetImage;
    }

    /**
     * @param resource $image
     * @param int[]    $foregroundColor
     *
     * @return int
     */
    protected function calculateAdditionalWhiteSpace($image, array $foregroundColor): int
    {
        $width = imagesx($image);
        $height = imagesy($image);
        $foregroundColor = imagecolorallocate(
            $image,
            $foregroundColor['r'],
            $foregroundColor['g'],
            $foregroundColor['b']
        );
        $whitespace = $width;
        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $color = imagecolorat($image, $x, $y);
                if ($color == $foregroundColor || $x == $whitespace) {
                    $whitespace = min($whitespace, $x);
                    break;
                }
            }
        }

        return $whitespace;
    }

    /**
     * @param resource $sourceImage
     * @param string   $logoPath
     * @param int      $logoWidth
     *
     * @return resource
     */
    protected function addLogo($sourceImage, $logoPath, $logoWidth = null, $scale = false)
    {
        $logoContents = $this->transformLogo($logoPath, $logoWidth, $scale);
        $logoImage = $logoContents->image();
        $logoSourceWidth = $logoContents->width();
        $logoSourceHeight = $logoContents->height();
        $logoTargetWidth = $logoContents->targetWidth();
        $logoTargetHeight = $logoContents->targetHeight();

        $logoX = imagesx($sourceImage) / 2 - $logoTargetWidth / 2;
        $logoY = imagesy($sourceImage) / 2 - $logoTargetHeight / 2;

        imagecopyresampled(
            $sourceImage,
            $logoImage,
            $logoX,
            $logoY,
            0,
            0,
            $logoTargetWidth,
            $logoTargetHeight,
            $logoSourceWidth,
            $logoSourceHeight
        );

        return $sourceImage;
    }

    /**
     * @param resource       $sourceImage
     * @param LabelInterface $label
     * @param int[]          $foregroundColor
     * @param int[]          $backgroundColor
     *
     * @throws BadMethodCallException
     * @return resource
     */
    protected function addLabel(
        $sourceImage,
        LabelInterface $label,
        array $foregroundColor,
        array $backgroundColor
    ) {
        $labelText = $label->getText();
        $labelFontSize = $label->getFontSize();
        $labelFontPath = $label->getFont();
        $labelMargin = $label->getMargins();
        $labelAlignment = $label->getAlignment();

        $labelBox = imagettfbbox($labelFontSize, 0, $labelFontPath, $labelText);
        $labelBoxWidth = ($labelBox[2] - $labelBox[0]);
        $labelBoxHeight = ($labelBox[0] - $labelBox[7]);
        $sourceWidth = imagesx($sourceImage);
        $sourceHeight = imagesy($sourceImage);
        $targetWidth = $sourceWidth;
        $targetHeight = $sourceHeight + $labelBoxHeight + $labelMargin['t'] + $labelMargin['b'];

        // Create empty target image
        $targetImage = imagecreatetruecolor($targetWidth, $targetHeight);
        $foregroundColor = imagecolorallocate(
            $targetImage,
            $foregroundColor['r'],
            $foregroundColor['g'],
            $foregroundColor['b']
        );
        $backgroundColor = imagecolorallocate(
            $targetImage,
            $backgroundColor['r'],
            $backgroundColor['g'],
            $backgroundColor['b']
        );
        imagefill($targetImage, 0, 0, $backgroundColor);
        // Copy source image to target image
        imagecopyresampled(
            $targetImage,
            $sourceImage,
            0,
            0,
            0,
            0,
            $sourceWidth,
            $sourceHeight,
            $sourceWidth,
            $sourceHeight
        );
        switch ($labelAlignment) {
            case LabelInterface::ALIGN_LEFT:
                $labelX = $labelMargin['l'];
                break;
            case LabelInterface::ALIGN_RIGHT:
                $labelX = $targetWidth - $labelBoxWidth - $labelMargin['r'];
                break;
            default:
                $labelX = (int)($targetWidth / 2 - $labelBoxWidth / 2);
                break;
        }
        $labelY = $targetHeight - $labelMargin['b'];
        imagettftext($targetImage, $labelFontSize, 0, $labelX, $labelY, $foregroundColor, $labelFontPath, $labelText);

        return $targetImage;
    }
}
