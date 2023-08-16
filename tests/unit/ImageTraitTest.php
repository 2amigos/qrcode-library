<?php

namespace unit;

use Da\QrCode\QrCode;
use Da\QrCode\Writer\PngWriter;

class ImageTraitTest extends \Codeception\Test\Unit
{
    public function testWithValidateResult()
    {
        $this->expectNotToPerformAssertions();

        $writer = new PngWriter();
        $qrCode = (new QrCode('hola@2amigos.us'));

        $writer->validateResult(true);
        $writer->writeString($qrCode);
    }

    public function testWithNoMargin()
    {
        $writer = new PngWriter();
        $qrCode = (new QrCode('hola@2amigos.us'))
            ->setMargin(0);

        $out = $writer->writeString($qrCode);

        $this->assertEquals(file_get_contents(codecept_data_dir('data-zero-margin.png')), $out);
    }
}