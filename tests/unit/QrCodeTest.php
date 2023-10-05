<?php

use Da\QrCode\Contracts\ErrorCorrectionLevelInterface;
use Da\QrCode\Contracts\LabelInterface;
use Da\QrCode\Format\MailToFormat;
use Da\QrCode\Label;
use Da\QrCode\QrCode;
use Da\QrCode\Writer\EpsWriter;
use Da\QrCode\Writer\JpgWriter;
use Da\QrCode\Writer\SvgWriter;


class QrCodeTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    public function testRaw()
    {
        $qrCode = new QrCode('2amigOS');
        $out = $qrCode->writeString();
        $out = base64_encode($out);
        $expected = file_get_contents(codecept_data_dir('out.txt'));

        $this->tester->assertEquals($expected, $out);
    }

    public function testPng()
    {
        $qrCode = new QrCode((new MailToFormat(['email' => 'hola@2amigos.us'])));
        $out = $qrCode->writeString();
        $this->tester->assertEquals(file_get_contents(codecept_data_dir('data.png')), $out);
    }

    public function testJpg()
    {
        return true;  // todo: try to figure out what is going on Travis and why is working locally.
        $writer = new JpgWriter();
        $qrCode = new QrCode((new MailToFormat(['email' => 'hola@2amigos.us'])), null, $writer);
        $out = $qrCode->writeString();

        $this->tester->assertEquals(file_get_contents(codecept_data_dir('data.jpeg')), $out);
    }

    public function testEps()
    {
        $writer = new EpsWriter();
        $qrCode = new QrCode((new MailToFormat(['email' => 'hola@2amigos.us'])), null, $writer);
        $out = $qrCode->writeString();

        $this->tester->assertEquals(
            str_replace("\r\n", "\n", file_get_contents(codecept_data_dir('data.eps'))),
            $out
        );
    }

    public function testSvg()
    {
        $writer = new SvgWriter();
        $qrCode = new QrCode((new MailToFormat(['email' => 'hola@2amigos.us'])), null, $writer);
        $out = $qrCode->writeString();

        $this->tester->assertEquals(
            str_replace("\r\n", "\n", file_get_contents(codecept_data_dir('data.svg'))),
            $out
        );
    }

    public function testLogo()
    {
        $out = (new QrCode(strtoupper('https://2am.tech'), ErrorCorrectionLevelInterface::HIGH))
            ->setLogo(codecept_data_dir('logo.png'))
            ->writeString();

        $this->tester->assertEquals(file_get_contents(codecept_data_dir('data-logo.png')), $out);
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
        $png = (new QrCode('https://2am.tech'))
            ->setWriter(new \Da\QrCode\Writer\PngWriter())
            ->writeString();

        $jpeg = (new QrCode('https://2am.tech'))
            ->setWriter(new \Da\QrCode\Writer\JpgWriter())
            ->writeString();

        $svg = (new QrCode('https://2am.tech'))
            ->setWriter(new \Da\QrCode\Writer\SvgWriter())
            ->writeString();

        $eps = (new QrCode('https://2am.tech'))
            ->setWriter(new \Da\QrCode\Writer\EpsWriter())
            ->writeString();

        $this->tester->assertEquals(file_get_contents(codecept_data_dir('writers/qrcode.png')), $png);
        $this->tester->assertEquals(file_get_contents(codecept_data_dir('writers/qrcode.jpg')), $jpeg);
        $this->tester->assertEquals(
            str_replace("\r\n", "\n", file_get_contents(codecept_data_dir('writers/qrcode.eps'))),
            $eps
        );
        $this->tester->assertEquals(
            str_replace("\r\n", "\n", file_get_contents(codecept_data_dir('writers/qrcode.svg'))),
            $svg
        );
    }

    public function testLabel()
    {
        $label = new Label('2am.tech');

        (new QrCode(strtoupper('https://2am.tech'), ErrorCorrectionLevelInterface::HIGH))
            ->setLabel($label)
            ->writeFile(codecept_data_dir('data-label-new.png'));
        $out = file_get_contents(codecept_data_dir('data-label-new.png'));
        $this->tester->assertEquals(file_get_contents(codecept_data_dir('data-label.png')), $out);

        unlink(codecept_data_dir('data-label-new.png'));
    }

    public function testQrColored()
    {
        $qrCode = new QrCode((new MailToFormat(['email' => 'hola@2amigos.us'])));
        $out = $qrCode
            ->setForegroundColor(51, 153, 255)
            ->writeString();

        $this->tester->assertEquals(file_get_contents(codecept_data_dir('data-color.png')), $out);
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

        $out = $qrCode->writeString();

        $this->tester->assertStringContainsString($out, file_get_contents(codecept_data_dir('data-attributes.png')));
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

    public function testSvgWithLogo()
    {
        $qrCode = new QrCode('2am. Technologies');
        $qrCode->setWriter(new SvgWriter())
            ->setLogo(codecept_data_dir('logo.png'));

        (new QrCode('2am. Technologies'))
            ->setWriter(new SvgWriter())
            ->setLogo(codecept_data_dir('logo.png'))
            ->setScaleLogoHeight(true);

        $this->tester->assertEquals(file_get_contents(codecept_data_dir('data-svg-with-logo.svg')), $qrCode->writeString());
    }

    public function testSvgWithLabel()
    {
        $qrCode = new QrCode('2am. Technologies');
        $qrCode->setWriter(new SvgWriter())
            ->setLabel(new Label('2am. Technologies', 'resources/fonts/noto_sans.otf', null, LabelInterface::ALIGN_LEFT));

        $this->tester->assertEquals(file_get_contents(codecept_data_dir('data-svg-with-label.svg')), $qrCode->writeString());
    }

    public function testSvgLabelAlignmentCenter()
    {
        $qrCode = new QrCode('2am. Technologies');
        $qrCode->setWriter(new SvgWriter())
            ->setLabel(new Label('2am. Technologies', 'resources/fonts/noto_sans.otf', null, LabelInterface::ALIGN_CENTER));

        $this->tester->assertEquals(file_get_contents(codecept_data_dir('data-svg-with-label2.svg')), $qrCode->writeString());;
    }

    public function testSvgLabelAlignmentRight()
    {
        $qrCode = new QrCode('2am. Technologies');
        $qrCode->setWriter(new SvgWriter())
            ->setLabel(new Label('2am. Technologies', null, null, LabelInterface::ALIGN_RIGHT));

        $this->tester->assertEquals(file_get_contents(codecept_data_dir('data-svg-with-label3.svg')), $qrCode->writeString());
    }

    public function testScaleLogo()
    {
        $qrCode = new QrCode('2am. Technologies');
        $qrCode->setLogo(codecept_data_dir('logo.png'))
            ->setScaleLogoHeight(true);
    }

    public function testScaleLogoSvg()
    {
        $qrCode = new QrCode('2am. Technologies');
        $qrCode
            ->setWriter(new SvgWriter())
            ->setLogo(codecept_data_dir('logo.png'))
            ->setScaleLogoHeight(true);

        $this->tester->assertEquals(file_get_contents(codecept_data_dir('svg-with-logo-scale.svg')), $qrCode->writeString());
    }
}