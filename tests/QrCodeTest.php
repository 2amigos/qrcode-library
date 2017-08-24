<?php
/**
 *
 * TinyMceTest.php
 *
 * Date: 06/03/15
 * Time: 13:53
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 */

namespace tests;

use Da\QrCode\Contracts\ErrorCorrectionLevelInterface;
use Da\QrCode\Contracts\LabelInterface;
use Da\QrCode\Format\MailToFormat;
use Da\QrCode\Label;
use Da\QrCode\QrCode;
use Da\QrCode\Writer\EpsWriter;
use Da\QrCode\Writer\JpgWriter;
use Da\QrCode\Writer\SvgWriter;

class QrCodeTest extends \PHPUnit_Framework_TestCase
{
    public function testRaw()
    {
        $qrCode = new QrCode('2amigOS');
        $out = $qrCode->writeString();
        $out = base64_encode($out);
        $expected = file_get_contents(__DIR__ . '/data/out.txt');

        $this->assertEquals($expected, $out);
    }

    public function testPng()
    {
        $file = __DIR__ . '/data/data-test.png';
        $qrCode = new QrCode((new MailToFormat(['email' => 'hola@2amigos.us'])));
        $qrCode->writeFile($file);
        $this->assertFileEquals(__DIR__ . '/data/data.png', $file);
        @unlink($file);
    }

    public function testJpg()
    {
        $file = __DIR__ . '/data/data-test.jpg';
        $writer = new JpgWriter();
        $qrCode = new QrCode((new MailToFormat(['email' => 'hola@2amigos.us'])), null, $writer);
        $qrCode->writeFile($file);
        $this->assertFileEquals(__DIR__ . '/data/data.jpg', $file);
        @unlink($file);
    }


    public function testEps()
    {
        $file = __DIR__ . '/data/data-test.eps';
        $writer = new EpsWriter();
        $qrCode = new QrCode((new MailToFormat(['email' => 'hola@2amigos.us'])), null, $writer);
        $qrCode->writeFile($file);
        $this->assertFileEquals(__DIR__ . '/data/data.eps', $file);
        @unlink($file);
    }

    public function testSvg()
    {
        $file = __DIR__ . '/data/data-test.svg';
        $writer = new SvgWriter();
        $qrCode = new QrCode((new MailToFormat(['email' => 'hola@2amigos.us'])), null, $writer);
        $qrCode->writeFile($file);
        $this->assertFileEquals(__DIR__ . '/data/data.svg', $file);
        @unlink($file);
    }

    public function testLogo()
    {
        $file = __DIR__ . '/data/data-logo-test.png';

        (new QrCode(strtoupper('https://2amigos.us'), ErrorCorrectionLevelInterface::HIGH))
            ->useLogo(__DIR__ . '/data/logo.png')
            ->writeFile($file);

        $this->assertFileEquals(__DIR__ . '/data/data-logo.png', $file);
        @unlink($file);
    }

    public function testLabel()
    {
        $file = __DIR__ . '/data/data-label-test.png';

        $label = new Label('2amigos.us');

        (new QrCode(strtoupper('https://2amigos.us'), ErrorCorrectionLevelInterface::HIGH))
            ->setLabel($label)
            ->writeFile($file);

        $this->assertFileEquals(__DIR__ . '/data/data-label.png', $file);
        @unlink($file);
    }

    public function testQrColored()
    {
        $file = __DIR__ . '/data/data-color-test.png';
        $qrCode = new QrCode((new MailToFormat(['email' => 'hola@2amigos.us'])));
        $qrCode
            ->useForegroundColor(51, 153, 255)
            ->writeFile($file);
        $this->assertFileEquals(__DIR__ . '/data/data-color.png', $file);
        @unlink($file);
    }

    public function testAttributes()
    {
        $file = __DIR__ . '/data/data-attributes-test.png';

        $label = (new Label('2amigos'))
            ->useFont(__DIR__ . '/../resources/fonts/monsterrat.otf')
            ->updateFontSize(12);

        $this->assertEquals('2amigos', $label->getText());
        $this->assertEquals(LabelInterface::ALIGN_CENTER, $label->getAlignment());
        $margins = $label->getMargins();
        $this->assertEquals(0, $margins['t']);
        $this->assertEquals(10, $margins['r']);
        $this->assertEquals(10, $margins['b']);
        $this->assertEquals(10, $margins['l']);
        $this->assertEquals(realpath(__DIR__ . '/../resources/fonts/monsterrat.otf'), $label->getFont());
        $this->assertEquals(12, $label->getFontSize());


        $qrCode = (new QrCode('Test text'))
            ->useLogo(__DIR__ . '/data/logo.png')
            ->useForegroundColor(51, 153, 255)
            ->useBackgroundColor(200, 220, 210)
            ->useEncoding('UTF-8')
            ->setErrorCorrectionLevel(ErrorCorrectionLevelInterface::HIGH)
            ->setLogoWidth(60)
            ->setText('https://2amigos.us')
            ->setSize(300)
            ->setMargin(5)
            ->setLabel($label);

        $this->assertEquals(realpath(__DIR__ . '/data/logo.png'), $qrCode->getLogoPath());
        $foregroundColor = $qrCode->getForegroundColor();
        $this->assertEquals(51, $foregroundColor['r']);
        $this->assertEquals(153, $foregroundColor['g']);
        $this->assertEquals(255, $foregroundColor['b']);
        $backgroundColor = $qrCode->getBackgroundColor();
        $this->assertEquals(200, $backgroundColor['r']);
        $this->assertEquals(220, $backgroundColor['g']);
        $this->assertEquals(210, $backgroundColor['b']);
        $this->assertEquals('UTF-8', $qrCode->getEncoding());
        $this->assertEquals(ErrorCorrectionLevelInterface::HIGH, $qrCode->getErrorCorrectionLevel());
        $this->assertEquals(60, $qrCode->getLogoWidth());
        $this->assertEquals('https://2amigos.us', $qrCode->getText());
        $this->assertEquals('image/png', $qrCode->getContentType());
        $this->assertEquals($label, $qrCode->getLabel());
        $qrCode->writeFile($file);
        $this->assertFileEquals(__DIR__ . '/data/data-attributes.png', $file);
        @unlink($file);
    }
}
