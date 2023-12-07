<?php

namespace unit;
use Da\QrCode\Enums\Gradient;
use Da\QrCode\Factory\LaravelQrCodeFactory;
use Da\QrCode\QrCode;
use Da\QrCode\Writer\PngWriter;

class LaravelQrCodeFactoryTest extends \Codeception\Test\Unit
{
    protected $tester;

    public function testInvalidQrCodeFormatNumber()
    {
        $this->expectExceptionMessage('Invalid format. The given format class , `1` does not exists');

        LaravelQrCodeFactory::make('2am. Technologies', 1);
    }

    public function testInvalidQrCodeContentInteger()
    {
        $this->expectExceptionMessage('Invalid content. It should be String or Array, integer given');

        LaravelQrCodeFactory::make(100);
    }

    public function testInvalidQrCodeFormatInvalidClass()
    {
        $this->expectException(\Exception::class);

        LaravelQrCodeFactory::make('2am. Technologies', PngWriter::class);
    }

    public function testCreateGradientQrCode()
    {
        $foreground = [
            'r' => 255,
            'g' => 0,
            'b' => 0,
        ];
        $foreground2 = [
            'r' => 0,
            'g' => 0,
            'b' => 255,
            'a' => 30,
        ];

        $qrCode = LaravelQrCodeFactory::make(
            '2am. Technologies',
            null,
            $foreground,
            null,
            null,
            null,
            $foreground2
        )
        ->writeString();

        $uri = file_get_contents(codecept_data_dir('blade/qrcode-gradient.png'));

        $this->assertEquals($qrCode, $uri);

        $qrCodeRadial = LaravelQrCodeFactory::make(
            '2am. Technologies',
            null,
            $foreground,
            null,
            null,
            null,
            $foreground2,
            null,
            null,
            null,
            null,
            null,
            Gradient::GRADIENT_RADIAL
        )
        ->writeString();

        $uri = file_get_contents(codecept_data_dir('blade/qrcode-gradient-radial.png'));

        $this->assertEquals($qrCodeRadial, $uri);
    }
}