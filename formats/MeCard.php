<?php
/**
 * @copyright Copyright (c) 2013 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\formats;

use yii\base\InvalidConfigException;
use yii\validators\EmailValidator;

/**
 * Class MeCard formats a string to properly create a meCard 4.0 QrCode
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\formats
 */
class MeCard extends FormatAbstract
{

    /**
     * @var string the name
     */
    public $firstName;
    /**
     * @var string the full name
     */
    public $lastName;
    /**
     * @var string the nickname
     */
    public $nickName;
    /**
     * @var string the address
     */
    public $address;
    /**
     * @var string designates a text string to be set as the telephone number in the phonebook. (1 to 24 digits)
     */
    public $phone;
    /**
     * @var string designates a text string to be set as the videophone number in the phonebook. (1 to 24 digits)
     */
    public $videoPhone;
    /**
     * @var string the email
     */
    public $email;
    /**
     * @var string a date in the format YYYY-MM-DD or ISO 860
     */
    public $birthday;
    /**
     * @var string designates a text string to be set as the address in the phonebook. (0 or more characters)
     */
    public $url;
    /**
     * @var string designates a text string to be set as the memo in the phonebook. (0 or more characters)
     */
    public $note;
    /**
     * @var string designates a text string to be set as the kana name in the phonebook. (0 or more characters)
     */
    public $sound;

    /**
     * @return string
     */
    public function getText()
    {
        $data[] = "MECARD:";
        $data[] = "N:{$this->lastName}{$this->firstName}";
        $data[] = "SOUND:{$this->sound}";
        $data[] = "TEL:{$this->phone}";
        $data[] = "TEL-AV:{$this->videoPhone}";
        $data[] = $this->getFormattedEmail();
        $data[] = "NOTE:{$this->note}";
        $data[] = "BDAY:{$this->birthday}";
        $data[] = "ADR:{$this->address}";
        $data[] = "URL:{$this->url}";
        $data[] = "NICKNAME:{$this->nickname}";

        return implode(";", $data);
    }

    /**
     * @return string the formatted email. Makes sure is a well formed email address.
     * @throws \yii\base\InvalidConfigException
     */
    protected function getFormattedEmail()
    {
        $validator = new EmailValidator();
        if (!$validator->validate($this->email, $error)) {
            throw new InvalidConfigException($error);
        }

        return "EMAIL:{$this->email}";
    }
}