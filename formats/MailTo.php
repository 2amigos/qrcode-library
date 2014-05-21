<?php
/**
 * @copyright Copyright (c) 2014 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\formats;

use yii\base\InvalidConfigException;
use yii\validators\EmailValidator;

/**
 * Class MailTo formats a string to properly create a MailID QrCode
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\formats
 */
class MailTo extends FormatAbstract
{
    /**
     * @var string the well formatted string to create the formatted email
     */
    public $email;

    /**
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        $validator = new EmailValidator();
        if (!$validator->validate($this->email, $error)) {
            throw new InvalidConfigException($error);
        }
    }

    /**
     * @inheritdoc
     */
    public function getText()
    {
        return "MAILTO:{$this->email}";
    }
}