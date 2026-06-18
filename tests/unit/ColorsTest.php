<?php

namespace unit;

use BaconQrCode\Renderer\Color\Rgb;
use Da\QrCode\Enums\Gradient;
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
            ->setForegroundColor(0, 255, 0, 75)
            ->setForegroundEndColor(0, 0, 255, 50)
            ->setBackgroundColor(200, 200, 200)
            ->setGradientType('x')
            ->writeString();

        $jpg = (new QrCode('2am Technologies'))
            ->setWriter(new JpgWriter())
            ->setForegroundColor(0, 255, 0, 25)
            ->setForegroundEndColor(0, 0, 255, 75)
            ->setBackgroundColor(200, 200, 200)
            ->setGradientType(Gradient::GRADIENT_DIAGONAL)
            ->writeString();

        $svg = (new QrCode('2am Technologies'))
            ->setWriter(new SvgWriter())
            ->setForegroundColor(0, 255, 0, 25)
            ->setForegroundEndColor(0, 0, 255, 95)
            ->setBackgroundColor(200, 200, 200)
            ->setGradientType(Gradient::GRADIENT_RADIAL)
            ->writeString();

        $png2 = (new QrCode('2am Technologies'))
            ->setWriter(new PngWriter())
            ->setForegroundColor(0, 255, 0, 80)
            ->setForegroundEndColor(0, 0, 255, 50)
            ->setBackgroundColor(200, 200, 200)
            ->setGradientType(Gradient::GRADIENT_INVERSE_DIAGONAL)
            ->writeString();

        $png3 = (new QrCode('2am Technologies'))
            ->setWriter(new PngWriter())
            ->setForegroundColor(0, 255, 0, 75)
            ->setForegroundEndColor(0, 0, 255, 100)
            ->setBackgroundColor(200, 200, 200)
            ->setGradientType(Gradient::GRADIENT_HORIZONTAL)
            ->writeString();

        $png4 = (new QrCode('2am Technologies'))
            ->setWriter(new PngWriter())
            ->setForegroundColor(0, 255, 0, 75)
            ->setForegroundEndColor(0, 0, 255, 100)
            ->setBackgroundColor(200, 200, 200)
            ->setGradientType(Gradient::GRADIENT_VERTICAL)
            ->writeString();

        // Raster gradients (GD) are validated structurally; SVG gradient output is deterministic.
        foreach ([$png, $png2, $png3, $png4] as $blob) {
            $this->assertSame('image/png', getimagesizefromstring($blob)['mime']);
        }
        $this->assertSame('image/jpeg', getimagesizefromstring($jpg)['mime']);

        $this->assertEquals(
            $this->normalizeString(file_get_contents(codecept_data_dir('colors/gradient.svg'))),
            $this->normalizeString($svg)
        );
    }

    /**
     * Regression test: radial / inverse-diagonal gradients used to render solid black because the
     * margin whitespace-trim looked for the exact foreground colour, which is absent at the image
     * edges of those gradients. The output must contain both ends of the colour spectrum.
     */
    public function testRadialGradientRendersColours()
    {
        $blob = (new QrCode('2am Technologies'))
            ->setForegroundColor(255, 0, 0)
            ->setForegroundEndColor(0, 0, 255)
            ->setGradientType(Gradient::GRADIENT_RADIAL)
            ->writeString();

        $image = imagecreatefromstring($blob);
        $width = imagesx($image);
        $height = imagesy($image);
        $hasReddish = false;
        $hasBluish = false;

        for ($y = 0; $y < $height; $y += 2) {
            for ($x = 0; $x < $width; $x += 2) {
                $color = imagecolorat($image, $x, $y);
                $red = ($color >> 16) & 0xFF;
                $green = ($color >> 8) & 0xFF;
                $blue = $color & 0xFF;

                if ($red > 120 && $blue < 80 && $green < 80) {
                    $hasReddish = true;
                }
                if ($blue > 120 && $red < 80 && $green < 80) {
                    $hasBluish = true;
                }
            }
        }

        $this->assertTrue($hasReddish, 'Radial gradient is missing the start (red) colour');
        $this->assertTrue($hasBluish, 'Radial gradient is missing the end (blue) colour');
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
            new Rgb(0, 0, 0),
            new Rgb(255, 255, 255)
        ))->setForegroundEndColor('x');
    }

    public function testInvalidBackgroundColorShouldThrowException()
    {
        $this->expectException(\Exception::class);

        (new StyleManager(
            new Rgb(0, 0, 0),
            new Rgb(255, 255, 255)
        ))->setBackgroundColor('x');
    }

    public function testForceRgb()
    {
        $this->expectNotToPerformAssertions();

        (new StyleManager(new Rgb(0, 0, 0), new Rgb(255, 255, 255)))
            ->forceUniformRgbColors();
    }

    protected function normalizeString($string)
    {
        return str_replace(
            "\r\n",
            "\n",
            str_replace("&#13;", "", $string)
        );
    }
}
