<?php

/*
 * This file is part of the 2amigos/qrcode-library project.
 *
 * (c) 2amigOS! <http://2am.tech/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\QrCode\Traits;

use Da\QrCode\Exception\InvalidConfigException;

/**
 * EmailTrait
 *
 * Provides methods to handle the email property
 *
 * @author Antonio Ramirez <hola@2amigos.us>
 * @link https://www.2amigos.us/
 * @package dosamigos\qrcode\traits
 */
trait EmailTrait
{
    /**
     * @var string a valid email
     */
    private $email;

    /**
     * @param string $value the email
     *
     * @throws InvalidConfigException
     */
    public function setEmail(string $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidConfigException('Email seems incorrect.');
        }

        $this->email = $value;
    }

    /**
     * @return string the email
     */
    public function getEmail(): string
    {
        return $this->email;
    }
}
