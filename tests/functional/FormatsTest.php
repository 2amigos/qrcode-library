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


use dosamigos\qrcode\formats\Bitcoin;
use dosamigos\qrcode\formats\BookMark;
use dosamigos\qrcode\formats\Geo;
use dosamigos\qrcode\formats\iCal;
use dosamigos\qrcode\formats\MailMessage;
use dosamigos\qrcode\formats\MailTo;
use dosamigos\qrcode\formats\MeCard;
use dosamigos\qrcode\formats\Mms;
use dosamigos\qrcode\formats\Phone;
use dosamigos\qrcode\formats\Sms;
use dosamigos\qrcode\formats\vCard;
use dosamigos\qrcode\formats\Wifi;
use dosamigos\qrcode\formats\Youtube;

class FormatsTest extends TestCase
{
    public function testBookMark() {
        $bookmark = new BookMark(['title' => 'test-title', 'url' => 'http://2amigos.us']);
        $this->assertEquals("http://2amigos.us", $bookmark->getUrl());
        // using __toString()
        $this->assertEquals("MEBKM:TITLE:test-title;URL:http://2amigos.us;;", $bookmark);
        $this->setExpectedException('yii\base\InvalidConfigException');
        $bookmark->url = 'wrong!url';
    }

    public function testBookMarkFailed()
    {
        $this->setExpectedException('yii\base\InvalidConfigException');
        $bookmark = new BookMark();
    }

    public function testGeo() {
        $geo = new Geo(['lat' => 1,'lng' => 1, 'altitude' => 20]);
        $this->assertEquals("GEO:1,1,20", $geo->getText());
    }

    public function testMailMessage() {
        $message = new MailMessage(['email' => 'hola@2amigos.us', 'subject' => 'test', 'body' => 'test-body']);
        $this->assertEquals("hola@2amigos.us", $message->getEmail());
        $this->assertEquals("MATMSG:TO:hola@2amigos.us;SUB:test;BODY:test-body;;", $message->getText());

        $this->setExpectedException('yii\base\InvalidConfigException');
        $message = new MailMessage(['email' => 'wrongaddress!!']);

    }

    public function testMailTo() {
        $mailTo = new MailTo(['email' => 'hola@2amigos.us']);
        $this->assertEquals("MAILTO:hola@2amigos.us", $mailTo->getText());

    }

    public function testMailToWrongEmail() {
        $this->setExpectedException('yii\base\InvalidConfigException');
        $mailTo = new MailTo(['email' => 'wrongaddress-@...']);
    }

    public function testMeCard() {
        $card = new MeCard();
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

        $this->setExpectedException('yii\base\InvalidConfigException');
        $card->email = 'wrongaddress!!!';
        $card->getText();

    }

    public function testPhone() {
        $phone = new Phone(['phone' => 657657657]);

        $this->assertEquals("TEL:657657657", $phone->getText());
    }

    public function testSms() {
        $sms = new Sms(['phone' => 657657657, 'msg' => 'test']);

        $this->assertEquals("SMSTO:657657657:test", $sms->getText());

        $sms->msg = null;
        $this->assertEquals("SMSTO:657657657", $sms->getText());
    }

    public function testMms() {
        $mms = new Mms(['phone' => 657657657, 'msg' => 'test']);

        $this->assertEquals("MMSTO:657657657:test", $mms->getText());

        $mms->msg = null;
        $this->assertEquals("MMSTO:657657657", $mms->getText());
    }

    public function testBitcoin() {
        $bitcoin = new Bitcoin(['address' => 'test-address', 'amount' => 1]);

        $this->assertEquals("bitcoin:test-address?amount=1", $bitcoin->getText());
    }

    public function testYoutube() {
        $yt = new Youtube(['videoId' => 123456]);

        $this->assertEquals("youtube://123456", $yt->getText());
    }

    public function testVCard() {
        $vcard = new vCard();
        $vcard->name = "Antonio";
        $vcard->fullName = "Antonio Ramirez";
        $vcard->email = "hola@2amigos.us";

        $expected = "BEGIN:VCARD\nVERSION:4.0\nN:Antonio\nFN:Antonio Ramirez\nADR:\nNICKNAME:\n" .
            "EMAIL;TYPE=PREF,INTERNET:hola@2amigos.us\nTEL;TYPE=WORK:\nTEL;TYPE=HOME:\nBDAY:\n" .
            "GENDER:\nIMPP:\nROLE:\nURL:\nORG:\nNOTE:\n" .
            "ORG:\nLANG:\nEND:VCARD";

        $this->assertEquals($expected, $vcard->getText());

        $this->setExpectedException('yii\base\InvalidConfigException');
        $vcard->email = "wrongaddress";

    }

    public function testVCardPhoto() {
        $vcard = new vCard();
        $vcard->photo = 'http://2amigos.us/img/logo.png';

        $class = new \ReflectionClass('dosamigos\\qrcode\\formats\\vCard');
        $method = $class->getMethod('getFormattedPhoto');
        $method->setAccessible(true);

        $value = $method->invoke($vcard);

        $this->assertEquals("PHOTO;VALUE=URL;TYPE=PNG:http://2amigos.us/img/logo.png", $value);

        $vcard->photo = null;
        $this->assertNull($method->invoke($vcard));

        $vcard->photo = 'wrongimage.superb';

        $this->setExpectedException('yii\base\InvalidConfigException');
        $method->invoke($vcard);
    }

    public function testWifi() {
        $wifi = new Wifi(['authentication' => 'WPA', 'ssid' => 'testSSID', 'password' => 'HAKUNAMATATA']);
        $this->assertEquals("WIFI:T:WPA;S:testSSID;P:HAKUNAMATATA;;", $wifi->getText());
        $wifi->hidden = 'true';
        $this->assertEquals("WIFI:T:WPA;S:testSSID;P:HAKUNAMATATA;H:true;", $wifi->getText());
        $this->setExpectedException('yii\base\InvalidConfigException');
        $wifi = new Wifi(['authentication' => 'WPA', 'password' => 'HAKUNAMATATA']);
    }

    public function testiCal() {
        $iCal = new iCal(['summary' => 'test-summary', 'dtStart' => 1260232200, 'dtEnd' => 1260318600]);

        $this->assertEquals("BEGIN:VEVENT\nSUMMARY:test-summary\nDTSTART:20091208T013000Z\nDTEND:20091209T013000Z\nEND:VEVENT", $iCal);
    }
}