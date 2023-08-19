<?php

/*
 * This file is part of the 2amigos/qrcode-library project.
 *
 * (c) 2amigOS! <http://2am.tech/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

use Da\QrCode\Exception\InvalidConfigException;
use Da\QrCode\Format\BookMarkFormat;
use Da\QrCode\Format\BtcFormat;
use Da\QrCode\Format\GeoFormat;
use Da\QrCode\Format\ICalFormat;
use Da\QrCode\Format\MailMessageFormat;
use Da\QrCode\Format\MailToFormat;
use Da\QrCode\Format\MeCardFormat;
use Da\QrCode\Format\MmsFormat;
use Da\QrCode\Format\PhoneFormat;
use Da\QrCode\Format\SmsFormat;
use Da\QrCode\Format\VCardFormat;
use Da\QrCode\Format\WifiFormat;
use Da\QrCode\Format\YoutubeFormat;

class FormatsTest extends \Codeception\Test\Unit
{
    /**
     * @var UnitTester
     */
    protected $tester;

    public function testBookMark()
    {
        $bookmark = new BookMarkFormat(['title' => 'test-title', 'url' => 'http://2am.tech']);
        $this->tester->assertEquals('http://2am.tech', $bookmark->getUrl());
        // using __toString()
        $this->tester->assertEquals('MEBKM:TITLE:test-title;URL:http://2am.tech;;', $bookmark);
        $this->tester->expectThrowable(InvalidConfigException::class, function () use ($bookmark) {
            $bookmark->url = 'wrong!url';
        });
    }

    public function testBookMarkFailed()
    {
        $this->tester->expectThrowable(InvalidConfigException::class, function () {
            $bookmark = new BookMarkFormat();
        });
    }

    public function testGeo()
    {
        $geo = new GeoFormat(['lat' => 1, 'lng' => 1, 'altitude' => 20]);
        $this->tester->assertEquals('GEO:1,1,20', $geo->getText());
    }

    public function testMailMessage()
    {
        $message = new MailMessageFormat(['email' => 'hola@2amigos.us', 'subject' => 'test', 'body' => 'test-body']);
        $this->tester->assertEquals('hola@2amigos.us', $message->getEmail());
        $this->tester->assertEquals('MATMSG:TO:hola@2amigos.us;SUB:test;BODY:test-body;;', $message->getText());

        $this->tester->expectThrowable(InvalidConfigException::class, function () {
            $message = new MailMessageFormat(['email' => 'wrongaddress!!']);
        });
    }

    public function testMailTo()
    {
        $mailTo = new MailToFormat(['email' => 'hola@2amigos.us']);
        $this->tester->assertEquals('MAILTO:hola@2amigos.us', $mailTo->getText());
    }

    public function testMailToWrongEmail()
    {
        $this->tester->expectThrowable(InvalidConfigException::class, function () {
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
        $card->url = 'http://2am.tech';
        $card->nickName = 'tonydspaniard';

        $expected = 'MECARD:N:Ramirez Antonio;SOUND:docomotaro;TEL:657657657;TEL-AV:657657657;EMAIL:hola@2amigos.us;' .
                    'NOTE:test-note;BDAY:19711201;ADR:test-address;URL:http://2am.tech;NICKNAME:tonydspaniard;;';
        $this->tester->assertEquals($expected, $card->getText());

        $this->tester->expectThrowable(InvalidConfigException::class, function () use ($card) {
            $card->email = 'wrongaddress!!!';
        });
    }

    public function testPhone()
    {
        $phone = new PhoneFormat(['phone' => 657657657]);

        $this->tester->assertEquals('TEL:657657657', $phone->getText());
    }

    public function testSms()
    {
        $sms = new SmsFormat(['phone' => 657657657]);

        $this->tester->assertEquals('SMS:657657657', $sms->getText());
    }

    public function testMms()
    {
        $mms = new MmsFormat(['phone' => 657657657, 'msg' => 'test']);

        $this->tester->assertEquals('MMSTO:657657657:test', $mms->getText());

        $mms->msg = null;
        $this->tester->assertEquals("MMSTO:657657657", $mms->getText());
    }

    public function testBitcoin()
    {
        $bitcoin = new BtcFormat(['address' => 'test-address', 'amount' => 1, 'name' => 'antonio']);

        $this->tester->assertEquals('bitcoin:test-address?amount=1&label=antonio', $bitcoin->getText());
    }

    public function testYoutube()
    {
        $yt = new YoutubeFormat(['videoId' => 123456]);

        $this->tester->assertEquals('youtube://123456', $yt->getText());
    }

    public function testVCard()
    {
        $vcard = new VCardFormat();
        $vcard->name = 'Antonio';
        $vcard->fullName = 'Antonio Ramirez';
        $vcard->setEmail('hola@2amigos.us');

        $expected = "BEGIN:VCARD\nVERSION:4.0\nN:Antonio\nFN:Antonio Ramirez\nADR:\nNICKNAME:\n" .
            "EMAIL;TYPE=PREF,INTERNET:hola@2amigos.us\nTEL;TYPE=WORK:\nTEL;TYPE=HOME:\nBDAY:\n" .
            "GENDER:\nCATEGORIES:\nIMPP:\nROLE:\nURL:\nORG:\nNOTE:\n" .
            "LANG:\nEND:VCARD";

        $this->tester->assertEquals($expected, $vcard->getText());

        $this->tester->expectThrowable(InvalidConfigException::class, function () use ($vcard) {
            $vcard->setEmail('wrongaddress');
        });
    }

    public function testVCardPhoto()
    {
        $vcard = new VCardFormat();
        $vcard->photo = 'http://2am.tech/img/logo.png';

        $class = new ReflectionClass(VCardFormat::class);
        $method = $class->getMethod('getFormattedPhoto');
        $method->setAccessible(true);

        $value = $method->invoke($vcard);

        $this->tester->assertEquals('PHOTO;VALUE=URL;TYPE=PNG:http://2am.tech/img/logo.png', $value);

        $vcard->photo = null;
        $this->tester->assertNull($method->invoke($vcard));

        $vcard->photo = 'wrongimage.superb';

        $this->tester->expectThrowable(InvalidConfigException::class, function () use ($vcard, $method) {
            $method->invoke($vcard);
        });
    }

    public function testWifi()
    {
        $wifi = new WifiFormat(['authentication' => 'WPA', 'ssid' => 'testSSID', 'password' => 'HAKUNAMATATA']);
        $this->tester->assertEquals('WIFI:T:WPA;S:testSSID;P:HAKUNAMATATA;;', $wifi->getText());
        $wifi->hidden = 'true';
        $this->tester->assertEquals('WIFI:T:WPA;S:testSSID;P:HAKUNAMATATA;H:true;', $wifi->getText());
        $this->tester->expectThrowable(InvalidConfigException::class, function () {
            $wifi = new WifiFormat(['authentication' => 'WPA', 'password' => 'HAKUNAMATATA']);
        });
    }

    public function testICal()
    {
        $iCal = new ICalFormat(
            ['summary' => 'test-summary', 'startTimestamp' => 1260232200, 'endTimestamp' => 1260318600]
        );

        $this->tester->assertEquals(
            "BEGIN:VEVENT\nSUMMARY:test-summary\nDTSTART:20091208T003000Z\nDTEND:20091209T003000Z\nEND:VEVENT",
            $iCal->getText()
        );
    }

    public function testMsm()
    {
        $msm = new MmsFormat(['phone' => 9966716, 'msg' => 'testing-message']);

        $this->tester->assertEquals('MMSTO:9966716:testing-message', $msm->getText());
    }

    public function testGettersAndSetters()
    {
        $testEMail = 'hola@2amigos.us';

        $emailFormat = new MailToFormat();
        $emailFormat->__set('email', $testEMail);

        $emailAddress = $emailFormat->__get('email');

        $this->assertEquals($testEMail, $emailAddress);
    }

    public function testGettersUnknownProperties()
    {
        $this->expectException('Da\QrCode\Exception\UnknownPropertyException');
        $testEMail = 'hola@2amigos.us';

        $emailFormat = new MailToFormat();
        $emailFormat->setEmail($testEMail);

        $emailFormat->__get('userEmail');
    }

    public function testSettersUnknownProperties()
    {
        $this->expectException('Da\QrCode\Exception\UnknownPropertyException');
        $testEMail = 'hola@2amigos.us';

        $emailFormat = new MailToFormat();
        $emailFormat->__set('userEmail', $testEMail);
    }

    public function testSetReadOnlyProperties()
    {
        $this->expectException('Da\QrCode\Exception\InvalidCallException');

        $bookMark = new BookMarkFormat(['title' => 'test-title', 'url' => 'http://2amigos.tech']);
        $bookMark->__set('text', 'bookmark content');
    }

    public function testIsset()
    {
        $testEMail = 'hola@2amigos.us';

        $emailFormat = new MailToFormat();
        $emailFormat->setEmail($testEMail);

        $this->assertTrue($emailFormat->__isset('email'));

        // invalid property
        $this->assertFalse($emailFormat->__isset('userEmail'));
    }

    public function testCallInvalidMethod()
    {
        $this->expectException('Da\QrCode\Exception\UnknownMethodException');

        $emailFormat = new MailToFormat();
        $emailFormat->setEmailAddress('hola@2amigos.us');
    }

    public function testFormatPropertiesAndMethodsExistence()
    {
        $emailFormat = new MailToFormat();

        $this->assertTrue($emailFormat->hasProperty('email'));
        $this->assertFalse($emailFormat->hasProperty('emailAddress'));

        $this->assertTrue($emailFormat->hasMethod('setEmail'));
        $this->assertFalse($emailFormat->hasMethod('setEmailAddress'));
    }

    protected function _before()
    {
    }

    protected function _after()
    {
    }
}
