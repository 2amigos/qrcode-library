<?php

/*
 * This file is part of the 2amigos/yii2-qrcode-component project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\QrCode\Format;

/**
 * Class Bitcoin formats a string to properly create a Bitcoin URI
 *
 * @package Da\QrCode\Format
 */
class BtcFormat extends AbstractFormat
{
    /**
     * @var string the Bitcoin address
     */
    public $address;

    /**
     * @var string the payable amount
     */
    public $amount;

    /**
     * @inheritdoc
     */
    public function getText()
    {
        return "bitcoin:{$this->address}?amount={$this->amount}";
    }
}
