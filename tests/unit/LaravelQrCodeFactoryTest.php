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



    protected function normalizeString($string)
    {
        return str_replace(
            "\r\n", "\n", str_replace(
                "&#13;", "", $string
            )
        );
    }
}