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

use dosamigos\qrcode\formats\MailTo;
use dosamigos\qrcode\lib\Enum;
use dosamigos\qrcode\QrCode;

class QrCodeHelperTest extends TestCase
{
    public function testRaw()
    {
        $out = QrCode::raw('2amigOS');
        $out = base64_encode(implode("", $out));
        $expected = "wcHBwcHBwcCEAgMCA8DBwcHBwcHBwcDAwMDAwcCFAwMDAsDBwMDAwMDBwcDBwcHAwcCFAgMDA8DBwMHBwcDBwcDBwcHAwcCEA" .
            "wIDAsDBwMHBwcDBwcDBwcHAwcCFAgICA8DBwMHBwcDBwcDAwMDAwcCFAwICAsDBwMDAwMDBwcHBwcHBwcCRkJGQkcDBwcHBwcHBwMDAwM" .
            "DAwMCFAwMDAsDAwMDAwMDAhYWEhYSEkYWEAgMDA4SFhYWEhYWEAwMDAwICkAMCAgMDAgICAwIDAgMDAgMCAgIDkQMCAwMCAwIDAgIDAwI" .
            "DAgIDAwMCkAICAwMCAgIDAgMDAgMCAgIDAgMDkQICAwMCAwMDAgMCAgIDwMDAwMDAwMCBAgMDAgMCAgMCAwMDwcHBwcHBwcCFAwMDAwIC" .
            "AwMCAwICwcDAwMDAwcCEAwMDAgMCAgMCAgIDwcDBwcHAwcCEAwMCAwMDAwMCAgIDwcDBwcHAwcCFAwICAgMDAwICAwMDwcDBwcHAwcCEA" .
            "gMCAwIDAwMDAwIDwcDAwMDAwcCFAgMDAgICAgICAgICwcHBwcHBwcCFAwIDAwICAwMDAgMC";
        $this->assertEquals($expected, $out);
    }

    public function testText()
    {
        $out = QrCode::text((new MailTo(['email' => 'hola@2amigos.us']))->getText());
        $out = implode("", $out);
        $expected = "111111100010100100111111110000010111101101010000011011101010111011001011101101110100101000110101" .
            "11011011101010001101101011101100000101100001100100000111111110101010101011111110000000011110100000000000" .
            "11010011001111100011101101111000101010101010010101101000111000100101110111111100101010001001010100011000" .
            "00100010100000110011101110001111100110001100101011001111011111101010110010110010001011011000001111101011" .
            "11101010111110110000000001110010010001011111111110100010101010101001000001001010001100010101101110100101" .
            "10001111101111011101010110101110101010101110100100110100110000110000010100011011001001011111111010100000" .
            "110111001";

        $this->assertEquals($expected, $out);
    }

    public function testPng()
    {
        $file = __DIR__ . '/data/data-test.png';
        QrCode::png((new MailTo(['email' => 'hola@2amigos.us']))->getText(), $file);
        $this->assertFileEquals(__DIR__ . '/data/data.png', $file);
        @unlink($file);
    }

    public function testJpg()
    {
        $file = __DIR__ . '/data/data-test.jpg';
        QrCode::jpg((new MailTo(['email' => 'hola@2amigos.us']))->getText(), $file);
        $this->assertFileEquals(__DIR__ . '/data/data.jpg', $file);
        @unlink($file);
    }

    public function testWrongFormat()
    {
        $this->setExpectedException('yii\base\InvalidParamException');
        $out = QrCode::encode('test-text', false, Enum::QR_ECLEVEL_L, 3, 4, false, 90);
    }
}