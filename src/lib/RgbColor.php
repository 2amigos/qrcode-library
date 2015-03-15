<?php
/**
 * @copyright Copyright (c) 2013-15 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\lib;

use yii\base\InvalidParamException;
use yii\base\Object;

/**
 * Class RgbColor
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\lib
 */
class RgbColor extends Object
{
    /**
     * @var array
     */
    private $_color = array(
        'red' => null,
        'green' => null,
        'blue' => null
    );

    /**
     * Constructor
     *
     * @param int $red
     * @param int $green
     * @param int $blue
     */
    public function __construct($red = 255, $green = 255, $blue = 255)
    {
        $this->red = $red;
        $this->green = $green;
        $this->blue = $blue;
    }

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidParamException
     */
    public function __set($name, $value)
    {
        if (array_key_exists($name, $this->_color)) {
            if ($value >= 0 && $value <= 255)
                return $this->_color[$name] = $value;
            else
                throw new InvalidParamException('Wrong RGB value');
        }
        parent::__set($name, $value);
    }

    /**
     * @inheritdoc
     */
    public function __get($name)
    {

        if (array_key_exists($name, $this->_color)) {
            return $this->_color[$name];
        }

        return parent::__get($name);
    }

    /**
     * @inheritdoc
     */
    public function __isset($name)
    {
        return isset($this->_color[$name]) || parent::__isset($name);
    }

    /**
     * @inheritdoc
     */
    public function __unset($name)
    {
        if (isset($this->_color[$name])) {
            $this->_color[$name] = null;
        } else {
            parent::__unset($name);
        }
    }
}

