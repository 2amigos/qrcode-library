<?php

namespace unit;

use Da\QrCode\Contracts\LabelInterface;
use Da\QrCode\Label;
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

    public function testWriteDataUri()
    {
        $uri = (new QrCode('hola@2amigos.us'))->writeDataUri();

        $this->assertEquals(file_get_contents(codecept_data_dir('data-uri.txt')), $uri);
    }

    public function testSetFontInvalidPath()
    {
        $this->expectException(\Da\QrCode\Exception\InvalidPathException::class);

        (new Label('2amigos'))
            ->setFont(__DIR__ . '/../../resources/fonts/invalid-font.otf')
            ->setFontSize(12);
    }

    public function testLabelAlignment()
    {
        $writer = new \Da\QrCode\Writer\PngWriter();
        $qrCode = (new QrCode('2amigos'))
            ->setLabel(new Label(
                'hola@2amigos.us',
                null,
                null,
                LabelInterface::ALIGN_CENTER,
            ));
        $out = $writer->writeString($qrCode);
        $this->assertEquals(file_get_contents(codecept_data_dir('data-label-center.png')), $out);

        $qrCode = (new QrCode('2amigos'))
            ->setLabel(new Label(
                'hola@2amigos.us',
                null,
                null,
                LabelInterface::ALIGN_LEFT,
            ));

        $out = $writer->writeString($qrCode);
        $this->assertEquals(file_get_contents(codecept_data_dir('data-label-left.png')), $out);

        $qrCode = (new QrCode('2amigos'))
            ->setLabel(new Label(
                'hola@2amigos.us',
                null,
                null,
                LabelInterface::ALIGN_RIGHT,
            ));

        $out = $writer->writeString($qrCode);
        $this->assertEquals(file_get_contents(codecept_data_dir('data-label-right.png')), $out);
    }

    public function validateImageStringOutput()
    {
        $this->expectException('Da\QrCode\Exception\ValidationException');

        $writer = new PngWriter();
        $qrCode = new QrCode('hola@2amigos.us');
        $imageString = $writer->writeString($qrCode);

        $writer->validateOutput($imageString, $qrCode->setMargin(15));
    }
}