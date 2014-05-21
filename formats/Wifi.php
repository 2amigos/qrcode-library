<?php
/**
 *
 * Mail.php
 *
 * Date: 21/05/14
 * Time: 13:37
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 */

namespace dosamigos\qrcode\formats;

use yii\base\InvalidConfigException;
use yii\validators\EmailValidator;

/**
 * Class Wifi formats a string to properly create a Wifi QrCode
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\formats
 */
class Wifi extends FormatAbstract
{

    /**
     * @var string the authentication type. e.g., WPA
     */
    public $type;
    /**
     * @var string the network SSID
     */
    public $ssid;
    /**
     * @var string the wifi password
     */
    public $password;

    /**
     * @return string
     */
    public function getText()
    {
        return "WIFI:T:{$this->type};S{$this->ssid};{$this->password}";
    }
}