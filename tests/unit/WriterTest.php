<?php

namespace unit;

use Da\QrCode\Factory\WriterFactory;
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


}