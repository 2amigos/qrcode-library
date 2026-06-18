<?php

namespace unit;

use Da\QrCode\Factory\WriterFactory;
use Da\QrCode\QrCode;
use Da\QrCode\Writer\EpsWriter;
use Da\QrCode\Writer\JpgWriter;
use Da\QrCode\Writer\PngWriter;
use Da\QrCode\Writer\SvgWriter;

class WriterTest extends \Codeception\Test\Unit
{
    public function testAbstractFactoryPng()
    {
        $writer = WriterFactory::fromName('png');

        $this->assertInstanceOf(PngWriter::class, $writer);
    }

    public function testAbstractFactoryJpg()
    {
        $writer = WriterFactory::fromName('jpg');

        $this->assertInstanceOf(JpgWriter::class, $writer);
    }

    public function testAbstractFactorySvg()
    {
        $writer = WriterFactory::fromName('svg');

        $this->assertInstanceOf(SvgWriter::class, $writer);
    }

    public function testAbstractFactoryEps()
    {
        $writer = WriterFactory::fromName('eps');

        $this->assertInstanceOf(EpsWriter::class, $writer);
    }

    public function testAbstractFactoryInvalidWriter()
    {
        $this->expectException('Da\QrCode\Exception\UnknownWriterException');

        WriterFactory::fromName('bmp');
    }

    public function testWriterMimetypes()
    {
        $writer = WriterFactory::fromName('png');
        $this->assertEquals('image/png', $writer->getContentType());

        $writer = WriterFactory::fromName('jpg');
        $this->assertEquals('image/jpeg', $writer->getContentType());

        $writer = WriterFactory::fromName('svg');
        $this->assertEquals('image/svg+xml', $writer->getContentType());

        $writer = WriterFactory::fromName('eps');
        $this->assertEquals('image/eps', $writer->getContentType());
    }

    public function testWriterDataUri()
    {
        $writer = WriterFactory::fromName('png');
        $qrCode = new QrCode('hola@2amigos.us');

        $out = $writer->writeDataUri($qrCode);

        $this->assertStringStartsWith('data:image/png;base64,', $out);
        $blob = base64_decode(substr($out, strlen('data:image/png;base64,')));
        $this->assertSame('image/png', getimagesizefromstring($blob)['mime']);
        $this->assertSame(
            'hola@2amigos.us',
            (new \Zxing\QrReader($blob, \Zxing\QrReader::SOURCE_TYPE_BLOB))->text()
        );
    }

    public function testGetWriterName()
    {
        $writer = WriterFactory::fromName('png');
        $this->assertEquals('png', $writer->getName());

        $writer = WriterFactory::fromName('jpg');
        $this->assertEquals('jpg', $writer->getName());

        $writer = WriterFactory::fromName('svg');
        $this->assertEquals('svg', $writer->getName());

        $writer = WriterFactory::fromName('eps');
        $this->assertEquals('eps', $writer->getName());
    }
}