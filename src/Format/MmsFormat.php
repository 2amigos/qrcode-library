<?php

/*
 * This file is part of the 2amigos/qrcode-library project.
 *
 * (c) 2amigOS! <http://2am.tech/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\QrCode\Format;

/**
 * Mms formats a string to properly create a Sms QrCode
 *
* @author Antonio Ramirez <hola@2amigos.us>
 * @link https://www.2amigos.us/
 * @package Da\QrCode\Format
 */
class MmsFormat extends SmsFormat
{
    /**
     * @var string
     */
    public $msg;

    /**
     * @return string
     * @codeCoverageIgnore
     */
    public function getText(): string
    {
        $data = [];
        $data[] = 'MMSTO';
        $data[] = $this->phone;
        $data[] = $this->msg;

        return implode(':', array_filter($data));
    }
}
