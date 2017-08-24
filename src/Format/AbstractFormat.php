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

use Da\QrCode\Contracts\FormatInterface;
use yii\base\Object;

/**
 * Abstract Class FormatAbstract for all formats
 *
 * @author Antonio Ramirez <hola@2amigos.us>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package Da\QrCode\Format
 */
abstract class AbstractFormat extends Object implements FormatInterface
{
    /**
     * @return string the string representation of the object
     */
    public function __toString()
    {
        return $this->getText();
    }
}
