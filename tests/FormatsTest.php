<?php
/**
 *
 * BookMarkTest.php
 *
 * Date: 12/03/15
 * Time: 14:01
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 */

namespace tests;


use Da\QrCode\Format\BookMarkFormat;
use Da\QrCode\Format\BtcFormat;
use Da\QrCode\Format\GeoFormat;
use Da\QrCode\Format\iCalFormat;
use Da\QrCode\Format\MailMessageFormat;
use Da\QrCode\Format\MailToFormat;
use Da\QrCode\Format\MeCardFormat;
use Da\QrCode\Format\MmsFormat;
use Da\QrCode\Format\PhoneFormat;
use Da\QrCode\Format\SmsFormat;
use Da\QrCode\Format\vCardFormat;
use Da\QrCode\Format\WifiFormat;
use Da\QrCode\Format\YoutubeFormat;

class FormatsTest extends \PHPUnit_Framework_TestCase
{
    public function testBookMark()
    {
        $bookmark = new BookMarkFormat(['title' => 'test-title', 'url' => 'http://2amigos.us']);
        $this->assertEquals("http://2amigos.us", $bookmark->getUrl());
        // using __toString()
        $this->assertEquals("MEBKM:TITLE:test-title;URL:http://2amigos.us;;", $bookmark);
        $this->expectException('Da\QrCode\Exception\InvalidConfigException');
        $bookmark->url = 'wrong!url';
    }

    public function testBookMarkFailed()
    {
        $this->expectException('Da\QrCode\Exception\InvalidConfigException');
        $bookmark = new BookMarkFormat();
    }

    public function testGeo()
    {
        $geo = new GeoFormat(['lat' => 1, 'lng' => 1, 'altitude' => 20]);
        $this->assertEquals("GEO:1,1,20", $geo->getText());
    }

    public function testMailMessage()
    {
        $message = new MailMessageFormat(['email' => 'hola@2amigos.us', 'subject' => 'test', 'body' => 'test-body']);
        $this->assertEquals("hola@2amigos.us", $message->getEmail());
        $this->assertEquals("MATMSG:TO:hola@2amigos.us;SUB:test;BODY:test-body;;", $message->getText());

        $this->expectException('Da\QrCode\Exception\InvalidConfigException');
        $message = new MailMessageFormat(['email' => 'wrongaddress!!']);

    }

    public function testMailTo()
    {
        $mailTo = new MailToFormat(['email' => 'hola@2amigos.us']);
        $this->assertEquals("MAILTO:hola@2amigos.us", $mailTo->getText());

    }

    public function testMailToWrongEmail()
    {
        $this->expectException('Da\QrCode\Exception\InvalidConfigException');
        $mailTo = new MailToFormat(['email' => 'wrongaddress-@...']);
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
        $this->assertEquals($expected, $card->getText());

        $this->expectException('Da\QrCode\Exception\InvalidConfigException');
        $card->email = 'wrongaddress!!!';
        $card->getText();

    }

    public function testPhone()
    {
        $phone = new PhoneFormat(['phone' => 657657657]);

        $this->assertEquals("TEL:657657657", $phone->getText());
    }

    public function testSms()
    {
        $sms = new SmsFormat(['phone' => 657657657]);

        $this->assertEquals("SMS:657657657", $sms->getText());
    }

    public function testMms()
    {
        $mms = new MmsFormat(['phone' => 657657657, 'msg' => 'test']);

        $this->assertEquals("MMSTO:657657657:test", $mms->getText());

        $mms->msg = null;
        $this->assertEquals("MMSTO:657657657", $mms->getText());
    }

    public function testBitcoin()
    {
        $bitcoin = new BtcFormat(['address' => 'test-address', 'amount' => 1, 'name' => 'antonio']);

        $this->assertEquals("bitcoin:test-address?amount=1&label=antonio", $bitcoin->getText());
    }

    public function testYoutube()
    {
        $yt = new YoutubeFormat(['videoId' => 123456]);

        $this->assertEquals("youtube://123456", $yt->getText());
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

        $this->assertEquals($expected, $vcard->getText());

        $this->expectException('Da\QrCode\Exception\InvalidConfigException');
        $vcard->email = "wrongaddress";

    }

    public function testVCardPhoto()
    {
        $vcard = new vCardFormat();
        $vcard->photo = 'http://2amigos.us/img/logo.png';

        $class = new \ReflectionClass('Da\\QrCode\\Format\\vCardFormat');
        $method = $class->getMethod('getFormattedPhoto');
        $method->setAccessible(true);

        $value = $method->invoke($vcard);

        $this->assertEquals("PHOTO;VALUE=URL;TYPE=PNG:http://2amigos.us/img/logo.png", $value);

        $vcard->photo = null;
        $this->assertNull($method->invoke($vcard));

        $vcard->photo = 'wrongimage.superb';

        $this->expectException('Da\QrCode\Exception\InvalidConfigException');
        $method->invoke($vcard);
    }

    public function testWifi()
    {
        $wifi = new WifiFormat(['authentication' => 'WPA', 'ssid' => 'testSSID', 'password' => 'HAKUNAMATATA']);
        $this->assertEquals("WIFI:T:WPA;S:testSSID;P:HAKUNAMATATA;;", $wifi->getText());
        $wifi->hidden = 'true';
        $this->assertEquals("WIFI:T:WPA;S:testSSID;P:HAKUNAMATATA;H:true;", $wifi->getText());
        $this->expectException('Da\QrCode\Exception\InvalidConfigException');
        $wifi = new WifiFormat(['authentication' => 'WPA', 'password' => 'HAKUNAMATATA']);
    }

    public function testiCal()
    {
        $iCal = new iCalFormat(
            ['summary' => 'test-summary', 'startTimestamp' => 1260232200, 'endTimestamp' => 1260318600]
        );

        $this->assertEquals(
            "BEGIN:VEVENT\nSUMMARY:test-summary\nDTSTART:20091208T003000Z\nDTEND:20091209T003000Z\nEND:VEVENT",
            $iCal->getText()
        );
    }
}
