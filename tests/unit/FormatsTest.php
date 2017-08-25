<?php

use Da\QrCode\Format\BookMarkFormat;
use Da\QrCode\Format\GeoFormat;
use Da\QrCode\Format\MailMessageFormat;
use Da\QrCode\Format\MailToFormat;
use Da\QrCode\Format\MeCardFormat;
use Da\QrCode\Format\PhoneFormat;
use Da\QrCode\Format\SmsFormat;
use Da\QrCode\Format\MmsFormat;
use Da\QrCode\Format\BtcFormat;
use Da\QrCode\Format\YoutubeFormat;
use Da\QrCode\Format\vCardFormat;
use Da\QrCode\Format\iCalFormat;
use Da\QrCode\Format\WifiFormat;


class FormatsTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
    }

    protected function _after()
    {
    }

    public function testBookMark()
    {
        $bookmark = new BookMarkFormat(['title' => 'test-title', 'url' => 'http://2amigos.us']);
        $this->tester->assertEquals("http://2amigos.us", $bookmark->getUrl());
        // using __toString()
        $this->tester->assertEquals("MEBKM:TITLE:test-title;URL:http://2amigos.us;;", $bookmark);
        $this->tester->expectException('Da\QrCode\Exception\InvalidConfigException', function() use ($bookmark) {
            $bookmark->url = 'wrong!url';
        });
    }

    public function testBookMarkFailed()
    {
        $this->tester->expectException('Da\QrCode\Exception\InvalidConfigException', function(){
            $bookmark = new BookMarkFormat();
        });
    }

    public function testGeo()
    {
        $geo = new GeoFormat(['lat' => 1, 'lng' => 1, 'altitude' => 20]);
        $this->tester->assertEquals("GEO:1,1,20", $geo->getText());
    }

    public function testMailMessage()
    {
        $message = new MailMessageFormat(['email' => 'hola@2amigos.us', 'subject' => 'test', 'body' => 'test-body']);
        $this->tester->assertEquals("hola@2amigos.us", $message->getEmail());
        $this->tester->assertEquals("MATMSG:TO:hola@2amigos.us;SUB:test;BODY:test-body;;", $message->getText());

        $this->tester->expectException('Da\QrCode\Exception\InvalidConfigException', function() {
            $message = new MailMessageFormat(['email' => 'wrongaddress!!']);
        });
    }

    public function testMailTo()
    {
        $mailTo = new MailToFormat(['email' => 'hola@2amigos.us']);
        $this->tester->assertEquals("MAILTO:hola@2amigos.us", $mailTo->getText());

    }

    public function testMailToWrongEmail()
    {
        $this->tester->expectException('Da\QrCode\Exception\InvalidConfigException', function() {
            $mailTo = new MailToFormat(['email' => 'wrongaddress-@...']);
        });

    }

    public function testMeCard()
    {
        $card = new MeCardFormat();
        $card->firstName = 'Antonio';
        $card->lastName = 'Ramirez';
        $card->sound = 'docomotaro';
        $card->phone = '657657657';
        $card->videoPhone = '657657657';
        $card->email = 'hola@2amigos.us';
        $card->note = 'test-note';
        $card->birthday = '19711201';
        $card->address = 'test-address';
        $card->url = 'http://2amigos.us';
        $card->nickName = 'tonydspaniard';

        $expected = "MECARD:\nN:Ramirez Antonio;\nSOUND:docomotaro;\nTEL:657657657;\nTEL-AV:657657657;\nEMAIL:hola@2amigos.us;\n" .
            "NOTE:test-note;\nBDAY:19711201;\nADR:test-address;\nURL:http://2amigos.us;\nNICKNAME:tonydspaniard;\n;";
        $this->tester->assertEquals($expected, $card->getText());

        $this->tester->expectException('Da\QrCode\Exception\InvalidConfigException', function() use ($card){
            $card->email = 'wrongaddress!!!';
        });
    }

    public function testPhone()
    {
        $phone = new PhoneFormat(['phone' => 657657657]);

        $this->tester->assertEquals("TEL:657657657", $phone->getText());
    }

    public function testSms()
    {
        $sms = new SmsFormat(['phone' => 657657657]);

        $this->tester->assertEquals("SMS:657657657", $sms->getText());
    }

    public function testMms()
    {
        $mms = new MmsFormat(['phone' => 657657657, 'msg' => 'test']);

        $this->tester->assertEquals("MMSTO:657657657:test", $mms->getText());

        $mms->msg = null;
        $this->tester->assertEquals("MMSTO:657657657", $mms->getText());
    }

    public function testBitcoin()
    {
        $bitcoin = new BtcFormat(['address' => 'test-address', 'amount' => 1, 'name' => 'antonio']);

        $this->tester->assertEquals("bitcoin:test-address?amount=1&label=antonio", $bitcoin->getText());
    }

    public function testYoutube()
    {
        $yt = new YoutubeFormat(['videoId' => 123456]);

        $this->tester->assertEquals("youtube://123456", $yt->getText());
    }

    public function testVCard()
    {
        $vcard = new vCardFormat();
        $vcard->name = "Antonio";
        $vcard->fullName = "Antonio Ramirez";
        $vcard->email = "hola@2amigos.us";

        $expected = "BEGIN:VCARD\nVERSION:4.0\nN:Antonio\nFN:Antonio Ramirez\nADR:\nNICKNAME:\n" .
            "EMAIL;TYPE=PREF,INTERNET:hola@2amigos.us\nTEL;TYPE=WORK:\nTEL;TYPE=HOME:\nBDAY:\n" .
            "GENDER:\nIMPP:\nROLE:\nURL:\nORG:\nNOTE:\n" .
            "ORG:\nLANG:\nEND:VCARD";

        $this->tester->assertEquals($expected, $vcard->getText());

        $this->tester->expectException('Da\QrCode\Exception\InvalidConfigException', function() use ($vcard) {
            $vcard->email = "wrongaddress";
        });
    }

    public function testVCardPhoto()
    {
        $vcard = new vCardFormat();
        $vcard->photo = 'http://2amigos.us/img/logo.png';

        $class = new \ReflectionClass('Da\\QrCode\\Format\\vCardFormat');
        $method = $class->getMethod('getFormattedPhoto');
        $method->setAccessible(true);

        $value = $method->invoke($vcard);

        $this->tester->assertEquals("PHOTO;VALUE=URL;TYPE=PNG:http://2amigos.us/img/logo.png", $value);

        $vcard->photo = null;
        $this->tester->assertNull($method->invoke($vcard));

        $vcard->photo = 'wrongimage.superb';

        $this->tester->expectException('Da\QrCode\Exception\InvalidConfigException', function() use ($vcard, $method) {
            $method->invoke($vcard);
        });
    }

    public function testWifi()
    {
        $wifi = new WifiFormat(['authentication' => 'WPA', 'ssid' => 'testSSID', 'password' => 'HAKUNAMATATA']);
        $this->tester->assertEquals("WIFI:T:WPA;S:testSSID;P:HAKUNAMATATA;;", $wifi->getText());
        $wifi->hidden = 'true';
        $this->tester->assertEquals("WIFI:T:WPA;S:testSSID;P:HAKUNAMATATA;H:true;", $wifi->getText());
        $this->tester->expectException('Da\QrCode\Exception\InvalidConfigException', function() {
            $wifi = new WifiFormat(['authentication' => 'WPA', 'password' => 'HAKUNAMATATA']);
        });

    }

    public function testiCal()
    {
        $iCal = new iCalFormat(
            ['summary' => 'test-summary', 'startTimestamp' => 1260232200, 'endTimestamp' => 1260318600]
        );

        $this->tester->assertEquals(
            "BEGIN:VEVENT\nSUMMARY:test-summary\nDTSTART:20091208T003000Z\nDTEND:20091209T003000Z\nEND:VEVENT",
            $iCal->getText()
        );
    }

}
