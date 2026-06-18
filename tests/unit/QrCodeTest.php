<?php

use BaconQrCode\Renderer\RendererStyle\GradientType;
use Da\QrCode\Contracts\ErrorCorrectionLevelInterface;
use Da\QrCode\Contracts\LabelInterface;
use Da\QrCode\Format\MailToFormat;
use Da\QrCode\Label;
use Da\QrCode\QrCode;
use Da\QrCode\Writer\EpsWriter;
use Da\QrCode\Writer\JpgWriter;
use Da\QrCode\Writer\SvgWriter;
use Zxing\QrReader;

class QrCodeTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testRaw()
    {
        $out = (new QrCode('2amigOS'))->writeString();

        $this->assertPngString($out);
        $this->assertSame('2amigOS', $this->decode($out));
    }

    public function testPng()
    {
        $out = (new QrCode(new MailToFormat(['email' => 'hola@2amigos.us'])))->writeString();

        $this->assertPngString($out);
        $this->assertSame('MAILTO:hola@2amigos.us', $this->decode($out));
    }

    public function testJpg()
    {
        $writer = new JpgWriter();
        $out = (new QrCode(new MailToFormat(['email' => 'hola@2amigos.us']), null, $writer))->writeString();

        $info = getimagesizefromstring($out);
        $this->assertNotFalse($info);
        $this->assertSame('image/jpeg', $info['mime']);
        $this->assertSame('MAILTO:hola@2amigos.us', $this->decode($out));
    }

    public function testEps()
    {
        $out = (new QrCode(new MailToFormat(['email' => 'hola@2amigos.us']), null, new EpsWriter()))->writeString();

        $this->assertEquals(
            $this->normalizeString(file_get_contents(codecept_data_dir('data.eps'))),
            $this->normalizeString($out)
        );
    }

    public function testSvg()
    {
        $out = (new QrCode(new MailToFormat(['email' => 'hola@2amigos.us']), null, new SvgWriter()))->writeString();

        $this->assertEquals(
            $this->normalizeString(file_get_contents(codecept_data_dir('data.svg'))),
            $this->normalizeString($out)
        );
    }

    public function testLogo()
    {
        $out = (new QrCode(strtoupper('https://2am.tech'), ErrorCorrectionLevelInterface::HIGH))
            ->setLogo(codecept_data_dir('logo.png'))
            ->writeString();

        $this->assertPngString($out);
        $this->assertSame('HTTPS://2AM.TECH', $this->decode($out));
    }

    public function testLogoInvalidPath()
    {
        $this->expectException('Da\QrCode\Exception\InvalidPathException');

        (new QrCode(strtoupper('https://2am.tech'), ErrorCorrectionLevelInterface::HIGH))
            ->setLogo(codecept_data_dir('testing_logo.png'))
            ->writeString();
    }

    public function testSetOutputFormat()
    {
        $png = (new QrCode('https://2am.tech'))->setWriter(new \Da\QrCode\Writer\PngWriter())->writeString();
        $jpeg = (new QrCode('https://2am.tech'))->setWriter(new JpgWriter())->writeString();
        $svg = (new QrCode('https://2am.tech'))->setWriter(new SvgWriter())->writeString();
        $eps = (new QrCode('https://2am.tech'))->setWriter(new EpsWriter())->writeString();

        $this->assertSame('image/png', getimagesizefromstring($png)['mime']);
        $this->assertSame('https://2am.tech', $this->decode($png));
        $this->assertSame('image/jpeg', getimagesizefromstring($jpeg)['mime']);
        $this->assertSame('https://2am.tech', $this->decode($jpeg));

        $this->assertEquals(
            $this->normalizeString(file_get_contents(codecept_data_dir('writers/qrcode.svg'))),
            $this->normalizeString($svg)
        );
        $this->assertStringContainsString('%!PS-Adobe', $eps);
    }

    public function testLabel()
    {
        $label = new Label('2am.tech');

        $path = codecept_data_dir('data-label-new.png');
        (new QrCode(strtoupper('https://2am.tech'), ErrorCorrectionLevelInterface::HIGH))
            ->setLabel($label)
            ->writeFile($path);
        $out = file_get_contents($path);

        $this->assertPngString($out);
        $this->assertSame('HTTPS://2AM.TECH', $this->decode($out));

        unlink($path);
    }

    public function testQrColored()
    {
        $out = (new QrCode(new MailToFormat(['email' => 'hola@2amigos.us'])))
            ->setForegroundColor(51, 153, 255)
            ->writeString();

        // Coloured foreground still produces a valid PNG of the expected dimensions.
        $info = $this->assertPngString($out);
        $this->assertSame(320, $info[0]);
    }

    public function testAttributes()
    {
        $qrCode = (new QrCode('Test text'))
            ->setLogo(codecept_data_dir('logo.png'))
            ->setForegroundColor(51, 153, 255)
            ->setBackgroundColor(200, 220, 210)
            ->setEncoding('UTF-8')
            ->setErrorCorrectionLevel(ErrorCorrectionLevelInterface::HIGH)
            ->setLogoWidth(60)
            ->setText('https://2am.tech')
            ->setSize(300)
            ->setMargin(5);

        $this->tester->assertEquals(realpath(codecept_data_dir('logo.png')), $qrCode->getLogoPath());
        $foregroundColor = $qrCode->getForegroundColor();
        $this->tester->assertEquals(51, $foregroundColor['r']);
        $this->tester->assertEquals(153, $foregroundColor['g']);
        $this->tester->assertEquals(255, $foregroundColor['b']);
        $backgroundColor = $qrCode->getBackgroundColor();
        $this->tester->assertEquals(200, $backgroundColor['r']);
        $this->tester->assertEquals(220, $backgroundColor['g']);
        $this->tester->assertEquals(210, $backgroundColor['b']);
        $this->tester->assertEquals('UTF-8', $qrCode->getEncoding());
        $this->tester->assertEquals(ErrorCorrectionLevelInterface::HIGH, $qrCode->getErrorCorrectionLevel());
        $this->tester->assertEquals(60, $qrCode->getLogoWidth());
        $this->tester->assertEquals('https://2am.tech', $qrCode->getText());
        $this->tester->assertEquals('image/png', $qrCode->getContentType());

        $this->assertPngString($qrCode->writeString());
    }

    public function testLabelAttributes()
    {
        $label = (new Label('2amigos'))
            ->setFont(__DIR__ . '/../../resources/fonts/monsterrat.otf')
            ->setFontSize(12);

        $this->tester->assertEquals('2amigos', $label->getText());
        $this->tester->assertEquals(LabelInterface::ALIGN_CENTER, $label->getAlignment());
        $margins = $label->getMargins();
        $this->tester->assertEquals(0, $margins['t']);
        $this->tester->assertEquals(10, $margins['r']);
        $this->tester->assertEquals(10, $margins['b']);
        $this->tester->assertEquals(10, $margins['l']);
        $this->tester->assertEquals(realpath(__DIR__ . '/../../resources/fonts/monsterrat.otf'), $label->getFont());
        $this->tester->assertEquals(12, $label->getFontSize());
    }

    public function testQrCodeAlphaForeground()
    {
        $out = (new QrCode('2am. Technologies'))
            ->setForegroundColor(0, 0, 0, 50)
            ->writeString();

        // Semi-transparent foreground still yields a valid PNG.
        $this->assertPngString($out);
    }

    public function testSvgWithLogo()
    {
        $svg = (new QrCode('2am. Technologies'))
            ->setWriter(new SvgWriter())
            ->setLogo(codecept_data_dir('logo.png'))
            ->writeString();

        $this->assertValidSvg($svg);
        $this->assertStringContainsString('<image', $svg);
        $this->assertStringContainsString('data:image/png;base64', $svg);
    }

    public function testSvgWithLabel()
    {
        $svg = (new QrCode('2am. Technologies'))
            ->setWriter(new SvgWriter())
            ->setLabel(new Label('2am. Technologies', 'resources/fonts/noto_sans.otf', null, LabelInterface::ALIGN_LEFT))
            ->writeString();

        $this->assertValidSvg($svg);
        $this->assertStringContainsString('2am. Technologies', $svg);
        $this->assertStringContainsString('@font-face', $svg);
    }

    public function testSvgLabelAlignmentCenter()
    {
        $svg = (new QrCode('2am. Technologies'))
            ->setWriter(new SvgWriter())
            ->setLabel(new Label('2am. Technologies', 'resources/fonts/noto_sans.otf', null, LabelInterface::ALIGN_CENTER))
            ->writeString();

        $this->assertValidSvg($svg);
        $this->assertStringContainsString('2am. Technologies', $svg);
    }

    public function testSvgLabelAlignmentRight()
    {
        $svg = (new QrCode('2am. Technologies'))
            ->setWriter(new SvgWriter())
            ->setLabel(new Label('2am. Technologies', null, null, LabelInterface::ALIGN_RIGHT))
            ->writeString();

        $this->assertValidSvg($svg);
        $this->assertStringContainsString('2am. Technologies', $svg);
    }

    public function testScaleLogo()
    {
        $this->expectNotToPerformAssertions();

        (new QrCode('2am. Technologies'))
            ->setLogo(codecept_data_dir('logo.png'))
            ->setScaleLogoHeight(true);
    }

    public function testScaleLogoSvg()
    {
        $svg = (new QrCode('2am. Technologies'))
            ->setWriter(new SvgWriter())
            ->setLogo(codecept_data_dir('logo.png'))
            ->setScaleLogoHeight(true)
            ->writeString();

        $this->assertValidSvg($svg);
        $this->assertStringContainsString('<image', $svg);
    }

    public function testUnsetForegroundEndColor()
    {
        $qrCode = (new QrCode('2am Technologies'))
            ->setForegroundEndColor(255, 255, 255);

        $color = $qrCode->getForegroundEndColor();

        $this->assertIsArray($color);
        $this->assertEquals($color['r'], 255);
        $this->assertEquals($color['g'], 255);
        $this->assertEquals($color['b'], 255);

        $qrCode->unsetForegroundEndColor();
        $this->assertNull($qrCode->getForegroundEndColor());
    }

    public function testGetPathIntensity()
    {
        $qrCode = (new QrCode('2am Technologies'));
        $this->assertEquals($qrCode->getPathIntensity(), 1);
    }

    public function testGetGradientType()
    {
        $qrCode = (new QrCode('2am Technologies'));
        $this->assertEquals($qrCode->getGradientType(), GradientType::VERTICAL());
    }

    /**
     * Decodes a QR image blob back to its text payload using the bundled reader.
     */
    private function decode(string $blob): ?string
    {
        return (new QrReader($blob, QrReader::SOURCE_TYPE_BLOB))->text();
    }

    /**
     * Asserts the blob is a valid PNG image and returns its getimagesize() info.
     *
     * @return array<int|string, mixed>
     */
    private function assertPngString(string $blob): array
    {
        $info = getimagesizefromstring($blob);
        $this->assertNotFalse($info, 'Output is not a valid image');
        $this->assertSame('image/png', $info['mime']);

        return $info;
    }

    private function assertValidSvg(string $svg): void
    {
        $this->assertStringContainsString('<svg', $svg);
        $this->assertNotFalse(simplexml_load_string($svg), 'Output is not valid SVG/XML');
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
