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
 * Class MailMessage formats a string to properly create a NNTMail QrCode
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\formats
 */
class MailMessage extends FormatAbstract
{
    /**
     * @var string the email to
     */
    public $email;
    /**
     * @var string the subject
     */
    public $subject;
    /**
     * @var string the body of the mail message
     */
    public $body;

    /**
     * @inheritdoc
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
        return "MATMSG:TO:{$this->email};SUB:{$this->subject};BODY:{$this->body};;";
    }
}