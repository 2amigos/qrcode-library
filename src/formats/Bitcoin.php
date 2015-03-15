<?php
/**
 * @copyright Copyright (c) 2014-2015 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\formats;

/**
 * Class Bitcoin formats a string to properly create a Bitcoin URI
 *
 * @package dosamigos\qrcode\formats
 */
class Bitcoin extends FormatAbstract
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
