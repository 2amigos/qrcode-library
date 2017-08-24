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
        $qrCode = new QrCode((new MailToFormat(['email' => 'hola@2amigos.us']))->getText());
        $qrCode->writeFile($file);
        $this->assertFileEquals(__DIR__ . '/data/data.png', $file);
        @unlink($file);
    }

    public function testJpg()
    {
        $file = __DIR__ . '/data/data-test.jpg';
        $writer = new JpgWriter();
        $qrCode = new QrCode((new MailToFormat(['email' => 'hola@2amigos.us']))->getText(), null, $writer);
        $qrCode->writeFile($file);
        $this->assertFileEquals(__DIR__ . '/data/data.jpg', $file);
        @unlink($file);
    }


    public function testEps()
    {
        $file = __DIR__ . '/data/data-test.eps';
        $writer = new EpsWriter();
        $qrCode = new QrCode((new MailToFormat(['email' => 'hola@2amigos.us']))->getText(), null, $writer);
        $qrCode->writeFile($file);
        $this->assertFileEquals(__DIR__ . '/data/data.eps', $file);
        @unlink($file);
    }

    public function testSvg()
    {
        $file = __DIR__ . '/data/data-test.svg';
        $writer = new SvgWriter();
        $qrCode = new QrCode((new MailToFormat(['email' => 'hola@2amigos.us']))->getText(), null, $writer);
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

        $label  = new Label('2amigos.us');

        (new QrCode(strtoupper('https://2amigos.us'), ErrorCorrectionLevelInterface::HIGH))
            ->setLabel($label)
            ->writeFile($file);

        $this->assertFileEquals(__DIR__ . '/data/data-label.png', $file);
        @unlink($file);
    }

    public function testQrColored() {
        $file = __DIR__ . '/data/data-color-test.png';
        $qrCode = new QrCode((new MailToFormat(['email' => 'hola@2amigos.us']))->getText());
        $qrCode
            ->useForegroundColor(51, 153, 255)
            ->writeFile($file);
        $this->assertFileEquals(__DIR__ . '/data/data-color.png', $file);
        @unlink($file);
    }
}
