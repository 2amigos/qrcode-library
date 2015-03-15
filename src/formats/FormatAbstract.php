<?php
/**
 * @copyright Copyright (c) 2013-15 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\formats;

use yii\base\Object;

/**
 * Abstract Class FormatAbstract for all formats
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\formats
 */
abstract class FormatAbstract extends Object
{
    /**
     * @return string the formatted string to be encoded
     */
    abstract public function getText();

    /**
     * @return string the string representation of the object
     */
    public function __toString()
    {
        return $this->getText();
    }
}