<?php

namespace Da\QrCode\Factory;

use Da\QrCode\Contracts\QrCodeInterface;
use Da\QrCode\Enums\Format;
use Da\QrCode\Format\AbstractFormat;
use Da\QrCode\Label;
use Da\QrCode\QrCode;
use Exception;

class LaravelQrCodeFactory
{
    /**
     * @param $content
     * @param string|null $format
     * @param array|null $foreground
     * @param array|null $background
     * @param string|null $style
     * @param float|null $intensity
     * @param array|null $foreground2
     * @param int|null $margin
     * @param int|null $size
     * @param string|null $logoPath
     * @param string|null $logoSize
     * @param bool|null $scaleLogoHeight
     * @param string|null $gradientType
     * @param string|null $label
     * @throws Exception
     * @return QrCode
     */
    public static function make(
        $content,
        ?string $format = null,
        ?array $foreground = null,
        ?array $background = null,
        ?string $style = null,
        ?float $intensity = null,
        ?array $foreground2 = null,
        ?int $margin = null,
        ?int $size = null,
        ?string $logoPath = null,
        ?string $logoSize = null,
        ?bool $scaleLogoHeight = null,
        ?string $gradientType = null,
        ?string $label = null,
        ?string $fontPath = null,
        ?int $fontSize = null,
        ?string $alignment = null
    ): QrCodeInterface {
        $qrCode = self::buildQrCode($content, $format);

        self::applyForeground($qrCode, $foreground);
        self::applyForeground2($qrCode, $foreground2, $gradientType);
        self::applyBackground($qrCode, $background);
        self::applyPathStyle($qrCode, $style, $intensity);
        self::applyMargin($qrCode, $margin);
        self::applySize($qrCode, $size);
        self::applyLogo($qrCode, $logoPath, $logoSize, $scaleLogoHeight);
        self::applyLabel($qrCode, $label, $fontPath, $fontSize, $alignment);

        return $qrCode;
    }

    /**
     * @param QrCodeInterface $qrCode
     * @param array|null $foreground
     * @return void
     */
    protected static function applyForeground(QrCodeInterface $qrCode, ?array $foreground): void
    {
        $foreground = $foreground ?: config('2am-qrcode.foreground');

        $qrCode->setForegroundColor(
            $foreground['r'],
            $foreground['g'],
            $foreground['b'],
            isset($foreground['a']) ? $foreground['a'] : 100,
        );
    }

    /**
     * @param QrCodeInterface $qrCode
     * @param array|null $foreground2
     * @param string|null $gradientType
     * @return void
     */
    protected static function applyForeground2(
        QrCodeInterface $qrCode,
        ?array $foreground2,
        ?string $gradientType
    ): void {
        if (is_null($foreground2)) {
            return;
        }

        $qrCode->setForegroundEndColor(
            $foreground2['r'],
            $foreground2['g'],
            $foreground2['b'],
            isset($foreground2['a']) ? $foreground2['a'] : 100,
        );

        if (is_null($gradientType)) {
            return;
        }

        $qrCode->setGradientType($gradientType);
    }

    /**
     * @param QrCodeInterface $qrCode
     * @param array|null $background
     * @return void
     */
    protected static function applyBackground(QrCodeInterface $qrCode, ?array $background): void
    {
        $background = $background ?: config('2am-qrcode.background');

        $qrCode->setbackgroundColor(
            $background['r'],
            $background['g'],
            $background['b'],
        );
    }

    /**
     * @param QrCodeInterface $qrCode
     * @param string|null $style
     * @param float|null $intensity
     * @return void
     */
    protected static function applyPathStyle(QrCodeInterface $qrCode, ?string $style, ?float $intensity): void
    {
        if (!is_null($style)) {
            $qrCode->setPathStyle($style);
        }

        if (!is_null($intensity)) {
            $qrCode->setPathIntensity($intensity);
        }
    }

    /**
     * @param QrCodeInterface $qrCode
     * @param int|null $margin
     * @return void
     */
    protected static function applyMargin(QrCodeInterface $qrCode, ?int $margin): void
    {
        $margin = $margin ?: config('2am-qrcode.margin');

        $qrCode->setMargin($margin);
    }

    /**
     * @param QrCodeInterface $qrCode
     * @param int|null $size
     * @return void
     */
    protected static function applySize(QrCodeInterface $qrCode, ?int $size): void
    {
        $size = $size ?: config('2am-qrcode.size');

        $qrCode->setSize($size);
    }

    /**
     * @param QrCodeInterface $qrCode
     * @param string|null $logoPath
     * @param int|null $logoSize
     * @param bool|null $scale
     * @return void
     * @throws \Da\QrCode\Exception\InvalidPathException
     */
    protected static function applyLogo(QrCodeInterface $qrCode, ?string $logoPath, ?int $logoSize, ?bool $scale): void
    {
        $logoPath = $logoPath ?: config('2am-qrcode.logoPath');
        $logoSize = $logoSize ?: config('2am-qrcode.logoSize');
        $scale = $scale ?: config('2am-qrcode.scaleLogoHeight');

        if (is_null($logoPath)) {
            return;
        }

        $qrCode->setLogo($logoPath);

        if (!is_null($logoSize) && is_numeric($logoSize)) {
            $qrCode->setLogoWidth((int)$logoSize);
        }

        if ($scale) {
            $qrCode->setScaleLogoHeight($scale);
        }
    }

    /**
     * @param string|array $content
     * @param string|null $format
     * @throws Exception
     * @return QrCode
     */
    protected static function buildQrCode($content, ?string $format): QrCodeInterface
    {
        self::validate($content, $format);

        if (is_null($format) || $format === Format::TEXT) {
            return is_array($content)
                ? new QrCode($content['text'])
                : new QrCode($content);
        }

        $qrCodeFormat = new $format($content);

        return new QrCode($qrCodeFormat->getText());
    }

    protected static function applyLabel(
        QrCodeInterface $qrCode,
        ?string $label = null,
        ?string $fontPath = null,
        ?int $size = null,
        ?string $alignment = null
    ): void {
        if (! is_null($label)) {
            $qrCode->setLabel(new Label(
                $label,
                $fontPath ?? config('2am-qrcode.label.fontPath'),
                $size ?? config('2am-qrcode.label.size'),
                $alignment ?? config('2am-qrcode.label.align')
            ));
        }
    }

    /**
     * @param string|array $content
     * @param string|null $format
     * @return void
     * @throws Exception
     */
    protected static function validate($content, ?string $format): void
    {
        if (! is_array($content) && ! is_string($content)) {
            throw new Exception(
                'Invalid content. It should be String or Array, '
                . gettype($content)
                . ' given'
            );
        }

        if (! is_null($format) && $format !== 'text' && ! class_exists($format)) {
            throw new Exception(
                'Invalid format. The given format class , `'
                . $format
                . '` does not exists'
            );
        }

        if (! is_null($format) && $format !== 'text' && ! (new $format($content)) instanceof AbstractFormat) {
            throw new Exception(
                'Invalid format. It should be instance of Enum or null, '
                . gettype($format)
                . ' given'
            );
        }
    }
}
