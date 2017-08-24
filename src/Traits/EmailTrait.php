<?php

/*
 * This file is part of the 2amigos/yii2-qrcode-component project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\QrCode\Traits;

use yii\base\InvalidConfigException;
use yii\validators\EmailValidator;

/**
 * EmailTrait
 *
 * Provides methods to handle the email property
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\traits
 */
trait EmailTrait
{
    /**
     * @var string a valid email
     */
    protected $email;

    /**
     * @param string $value the email
     *
     * @throws InvalidConfigException
     */
    public function setEmail($value)
    {
        $error = null;
        $validator = new EmailValidator();
        if (!$validator->validate($value, $error)) {
            throw new InvalidConfigException($error);
        }

        $this->email = $value;
    }

    /**
     * @return string the email
     */
    public function getEmail()
    {
        return $this->email;
    }
}
