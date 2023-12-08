<?php

namespace unit;

use BaconQrCode\Renderer\Color\Rgb;
use Da\QrCode\Contracts\ColorsInterface;
use Da\QrCode\Enums\Gradient;
use Da\QrCode\Factory\LaravelQrCodeFactory;
use Da\QrCode\QrCode;
use Da\QrCode\StyleManager;
use Da\QrCode\Writer\EpsWriter;
use Da\QrCode\Writer\JpgWriter;
use Da\QrCode\Writer\PngWriter;
use Da\QrCode\Writer\SvgWriter;

class ColorsTest extends \Codeception\Test\Unit
{
    public function testEpsUniform()
    {
        $eps = (new QrCode('2am Technologies', null, new EpsWriter()))
            ->writeDataUri();

        $eps2 = (new QrCode('2am Technologies'))
            ->setWriter(new EpsWriter())
            ->writeDataUri();

        $this->assertEquals(
            $this->normalizeString(file_get_contents(codecept_data_dir('colors/uniform.eps'))),
            $this->normalizeString($eps)
        );

        $this->assertEquals(
            $this->normalizeString(file_get_contents(codecept_data_dir('colors/uniform2.eps'))),
            $this->normalizeString($eps2)
        );
    }

    public function testGradientColors()
    {
        $png = (new QrCode('2am Technologies'))
            ->setWriter(new PngWriter())
            ->setForegroundColor(0, 255, 0,75)
            ->setForegroundEndColor(0, 0, 255,50)
            ->setBackgroundColor(200, 200, 200)
            ->setGradientType('x')
            ->writeString();

        $jpg = (new QrCode('2am Technologies'))
            ->setWriter(new JpgWriter())
            ->setForegroundColor(0, 255, 0,25)
            ->setForegroundEndColor(0, 0, 255,75)
            ->setBackgroundColor(200, 200, 200)
            ->setGradientType(Gradient::GRADIENT_DIAGONAL)
            ->writeString();

        $svg = (new QrCode('2am Technologies'))
            ->setWriter(new SvgWriter())
            ->setForegroundColor(0, 255, 0,25)
            ->setForegroundEndColor(0, 0, 255,95)
            ->setBackgroundColor(200, 200, 200)
            ->setGradientType(Gradient::GRADIENT_RADIAL)
            ->writeString();

        $png2 = (new QrCode('2am Technologies'))
            ->setWriter(new PngWriter())
            ->setForegroundColor(0, 255, 0,80)
            ->setForegroundEndColor(0, 0, 255,50)
            ->setBackgroundColor(200, 200, 200)
            ->setGradientType(Gradient::GRADIENT_INVERSE_DIAGONAL)
            ->writeString();

        $png3 = (new QrCode('2am Technologies'))
            ->setWriter(new PngWriter())
            ->setForegroundColor(0, 255, 0,75)
            ->setForegroundEndColor(0, 0, 255,100)
            ->setBackgroundColor(200, 200, 200)
            ->setGradientType(Gradient::GRADIENT_HORIZONTAL)
            ->writeString();

        $png4 = (new QrCode('2am Technologies'))
            ->setWriter(new PngWriter())
            ->setForegroundColor(0, 255, 0,75)
            ->setForegroundEndColor(0, 0, 255,100)
            ->setBackgroundColor(200, 200, 200)
            ->setGradientType(Gradient::GRADIENT_VERTICAL)
            ->writeString();

        $this->assertEquals(
            $this->normalizeString(file_get_contents(codecept_data_dir('colors/gradient.png'))),
            $this->normalizeString($png)
        );

        $this->assertEquals(
            $this->normalizeString(file_get_contents(codecept_data_dir('colors/gradient.jpg'))),
            $this->normalizeString($jpg)
        );

        $this->assertEquals(
            $this->normalizeString(file_get_contents(codecept_data_dir('colors/gradient.svg'))),
            $this->normalizeString($svg)
        );

        $this->assertEquals(
            $this->normalizeString(file_get_contents(codecept_data_dir('colors/gradient2.png'))),
            $this->normalizeString($png2)
        );

        $this->assertEquals(
            $this->normalizeString(file_get_contents(codecept_data_dir('colors/gradient3.png'))),
            $this->normalizeString($png3)
        );

        $this->assertEquals(
            $this->normalizeString(file_get_contents(codecept_data_dir('colors/gradient4.png'))),
            $this->normalizeString($png4)
        );
    }

    public function testFactoryCreateGradientQrCode()
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

        $this->assertEquals(
            $this->normalizeString($qrCode),
            $this->normalizeString($uri)
        );

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

        $this->assertEquals(
            $this->normalizeString($qrCodeRadial),
            $this->normalizeString($uri)
        );
    }

    public function testInvalidForegroundColorShouldThrowException()
    {
        $this->expectException(\Exception::class);

        new StyleManager('x', 'y');
    }

    public function testInvalidForegroundEndColorShouldThrowException()
    {
        $this->expectException(\Exception::class);

        (new StyleManager(
            new Rgb(0,0,0), new Rgb(255,255,255)
        ))->setForegroundEndColor('x');
    }

    public function testInvalidBackgroundColorShouldThrowException()
    {
        $this->expectException(\Exception::class);

        (new StyleManager(
            new Rgb(0,0,0), new Rgb(255,255,255)
        ))->setBackgroundColor('x');
    }

    public function testForceRgb()
    {
        $this->expectNotToPerformAssertions();

        (new StyleManager(new Rgb(0,0,0), new Rgb(255,255,255)))
            ->forceUniformRgbColors();
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