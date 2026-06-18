<?php

namespace unit;

use Da\QrCode\Contracts\LabelInterface;
use Da\QrCode\Label;
use Da\QrCode\QrCode;
use Da\QrCode\Writer\PngWriter;
use Zxing\QrReader;

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

        $this->assertSame('image/png', getimagesizefromstring($out)['mime']);
        $this->assertSame('hola@2amigos.us', $this->decode($out));
    }

    public function testWriteDataUri()
    {
        $uri = (new QrCode('hola@2amigos.us'))->writeDataUri();

        $this->assertStringStartsWith('data:image/png;base64,', $uri);

        $blob = base64_decode(substr($uri, strlen('data:image/png;base64,')));
        $this->assertSame('image/png', getimagesizefromstring($blob)['mime']);
        $this->assertSame('hola@2amigos.us', $this->decode($blob));
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
        $writer = new PngWriter();

        foreach ([LabelInterface::ALIGN_CENTER, LabelInterface::ALIGN_LEFT, LabelInterface::ALIGN_RIGHT] as $alignment) {
            $qrCode = (new QrCode('2amigos'))
                ->setLabel(new Label('hola@2amigos.us', null, null, $alignment));

            $out = $writer->writeString($qrCode);

            $this->assertSame('image/png', getimagesizefromstring($out)['mime']);
            $this->assertSame('2amigos', $this->decode($out));
        }
    }

    public function testValidateImageStringOutput()
    {
        $this->expectException('Da\QrCode\Exception\ValidationException');

        $writer = new PngWriter();
        $qrCode = new QrCode('hola@2amigos.us');
        $imageString = $writer->writeString($qrCode);

        $writer->validateOutput($imageString, $qrCode->setText('2amigos'));
    }

    private function decode(string $blob): ?string
    {
        return (new QrReader($blob, QrReader::SOURCE_TYPE_BLOB))->text();
    }
}
