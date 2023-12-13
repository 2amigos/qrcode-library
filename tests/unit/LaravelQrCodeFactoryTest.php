<?php

namespace unit;
use Da\QrCode\Enums\Format;
use Da\QrCode\Enums\Gradient;
use Da\QrCode\Factory\LaravelQrCodeFactory;
use Da\QrCode\QrCode;
use Da\QrCode\Writer\PngWriter;

class LaravelQrCodeFactoryTest extends \Codeception\Test\Unit
{
    protected $tester;

    public function testInvalidQrCodeFormatNumber()
    {
        $this->expectExceptionMessage('Invalid format. The given format class , `1` does not exists');

        LaravelQrCodeFactory::make('2am. Technologies', 1);
    }

    public function testInvalidQrCodeContentInteger()
    {
        $this->expectExceptionMessage('Invalid content. It should be String or Array, integer given');

        LaravelQrCodeFactory::make(100);
    }

    public function testInvalidQrCodeFormatInvalidClass()
    {
        $this->expectException(\Exception::class);

        LaravelQrCodeFactory::make('2am. Technologies', PngWriter::class);
    }

    public function testCreateGradientQrCode()
    {
        //@TODO investigate why tests fail on CI but works properly on local
        //@TODO remove file storing once it's done
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
        file_put_contents(codecept_data_dir('blade/qrcode-gradient.png'), $qrCode);
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

        file_put_contents(codecept_data_dir('blade/qrcode-gradient-radial.png'), $qrCodeRadial);
        $uri = file_get_contents(codecept_data_dir('blade/qrcode-gradient-radial.png'));

        $this->assertEquals(
            $this->normalizeString($qrCodeRadial),
            $this->normalizeString($uri)
        );
    }

    public function testFactoryFormatText()
    {
        $content = '2am. Technologies';
        $qrCode = LaravelQrCodeFactory::make(
            $content,
            Format::TEXT
        );

        $this->assertEquals($qrCode->getText(), $content);
    }

    public function testFactoryFormatBookMark()
    {
        $content = [
            'title' => '2am. Technologies',
            'url' => 'https://2am.tech',
        ];

        $qrCode = LaravelQrCodeFactory::make(
            $content,
            Format::BOOK_MARK
        );

        $this->assertTrue(
            str_contains($qrCode->getText(), 'MEBKM:TITLE:')
            && str_contains($qrCode->getText(), ';URL:')
        );
    }

    public function testFactoryFormatBtc()
    {
        $content = [
            'name' => '2am. Technologies',
            'amount' => 1,
            'message' => 'unt test',
        ];

        $qrCode = LaravelQrCodeFactory::make(
            $content,
            Format::BTC
        );

        $this->assertTrue(str_contains($qrCode->getText(), 'bitcoin:'));
    }

    public function testFactoryFormatGeo()
    {
        $content = [
            'lat' => 100,
            'lng' => 100,
            'altitude' => 1,
        ];

        $qrCode = LaravelQrCodeFactory::make(
            $content,
            Format::GEO
        );

        $this->assertTrue(str_contains($qrCode->getText(), 'GEO:'));
    }

    public function testFactoryFormatICal()
    {
        $content = [
            'summary' => 'unit test',
            'startTimestamp' => 1702451054,
            'endTimestamp' => 1702454654,
        ];

        $qrCode = LaravelQrCodeFactory::make(
            $content,
            Format::I_CAL
        );
        $content = $qrCode->getText();

        $this->assertTrue(
            str_contains($content, 'BEGIN:VEVENT')
            && str_contains($content, 'SUMMARY:')
            && str_contains($content, 'DTSTART:')
            && str_contains($content, 'DTEND:')
            && str_contains($content, 'END:VEVENT')
        );
    }

    public function testFactoryFormatMailMessage()
    {
        $content = [
            'subject' => 'unit test',
            'body' => 'unit test body',
            'email' => 'testing@2am.tech',
        ];

        $qrCode = LaravelQrCodeFactory::make(
            $content,
            Format::MAIL_MESSAGE
        );
        $content = $qrCode->getText();

        $this->assertTrue(
            str_contains($content, 'MATMSG:TO:')
            && str_contains($content, 'SUB:')
            && str_contains($content, 'BODY:')
        );
    }

    public function testFactoryFormatMailTo()
    {
        $content = [
            'email' => 'testing@2am.tech',
        ];

        $qrCode = LaravelQrCodeFactory::make(
            $content,
            Format::MAIL_TO
        );
        $content = $qrCode->getText();

        $this->assertTrue(str_contains($content, 'MAILTO:'));
    }

    public function testFactoryFormatMeCard()
    {
        $content = [
            'firstName' => 'unit',
            'lastName' => 'testing',
            'nickName' => 'unit testing',
            'address' => 'saint monica st',
            'phone' => '1 1111 221',
            'videoPhone' => '1 111 121',
            'birthday' => '05/11/1990',
            'note' => '',
            'email' => 'testing@2am.tech',
        ];

        $qrCode = LaravelQrCodeFactory::make(
            $content,
            Format::ME_CARD
        );
        $content = $qrCode->getText();

        $this->assertTrue(
            str_contains($content, 'MECARD:')
            && str_contains($content, 'N:')
            && str_contains($content, 'SOUND:')
            && str_contains($content, 'TEL:')
            && str_contains($content, 'TEL-AV:')
            && str_contains($content, 'EMAIL:')
            && str_contains($content, 'NOTE:')
            && str_contains($content, 'BDAY:')
            && str_contains($content, 'ADR:')
            && str_contains($content, 'URL:')
            && str_contains($content, 'NICKNAME:')
        );
    }

    public function testFactoryFormatMms()
    {
        $content = [
            'msg' => 'unit test',
            'phone' => '1 111 122',

        ];

        $qrCode = LaravelQrCodeFactory::make(
            $content,
            Format::MMS
        );
        $content = $qrCode->getText();

        $this->assertTrue(str_contains($content, 'MMSTO:'));
    }

    public function testFactoryFormatPhone()
    {
        $content = [
            'phone' => '1 111 122',
        ];

        $qrCode = LaravelQrCodeFactory::make(
            $content,
            Format::PHONE_FORMAT
        );
        $content = $qrCode->getText();

        $this->assertTrue(str_contains($content, 'TEL:'));
    }

    public function testFactoryFormatSms()
    {
        $content = [
            'phone' => '1 111 122',
        ];

        $qrCode = LaravelQrCodeFactory::make(
            $content,
            Format::SNS_FORMAT
        );
        $content = $qrCode->getText();

        $this->assertTrue(str_contains($content, 'SMS:'));
    }

    public function testFactoryFormatVCard()
    {
        $content = [
            'name' => 'testing',
            'fullName' => 'unit testing',
        ];

        $qrCode = LaravelQrCodeFactory::make(
            $content,
            Format::V_CARD
        );
        $content = $qrCode->getText();

        $this->assertTrue(
            str_contains($content, 'BEGIN:VCARD')
            && str_contains($content, 'END:VCARD')
        );
    }

    public function testFactoryFormatWifi()
    {
        $content = [
            'authentication' => 'wpa2',
            'ssid' => 'unit-testing',
            'password' => 'xxxxxxxxxx',
        ];

        $qrCode = LaravelQrCodeFactory::make(
            $content,
            Format::WIFI
        );
        $content = $qrCode->getText();

        $this->assertTrue(
            str_contains($content, 'WIFI:')
            && str_contains($content, 'S:')
            && str_contains($content, 'P:')
        );
    }

    public function testFactoryFormatYoutube()
    {
        $content = [
            'videoId' => '123456',
        ];

        $qrCode = LaravelQrCodeFactory::make(
            $content,
            Format::YOUTUBE
        );
        $content = $qrCode->getText();

        $this->assertTrue(
            str_contains($content, 'youtube://')
        );
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