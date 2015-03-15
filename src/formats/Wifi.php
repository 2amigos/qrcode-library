<?php
/**
 * @copyright Copyright (c) 2013-15 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
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
    public $authentication;
    /**
     * @var string the network SSID
     */
    public $ssid;
    /**
     * @var string the wifi password
     */
    public $password;
    /**
     * @var string hidden SSID (optional; equals false if omitted): either true or false
     */
    public $hidden;

    /**
     * @throws InvalidConfigException
     */
    public function init()
    {
        if ($this->ssid === null) {
            throw new InvalidConfigException("'ssid' cannot be null");
        }
    }

    /**
     * @inheritdoc
     */
    public function getText()
    {
        $data = [];
        $data[] = $this->authentication !== null ? "T:{$this->authentication}" : "";
        $data[] = "S:{$this->ssid}";
        $data[] = $this->password !== null ? "P:{$this->password}" : "";
        $data[] = $this->hidden !== null ? "H:{$this->hidden}" : "";
        return "WIFI:" . implode(";", $data) . ";";
    }
}