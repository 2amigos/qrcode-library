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
 * Class Geo formats a string to properly create a Geo QrCode
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\formats
 */
class Geo extends FormatAbstract
{
    public $lat;
    public $lng;
    public $altitude;

    /**
     * @inheritdoc
     */
    public function getText()
    {
        return "GEO:{$this->lat},{$this->lng},{$this->altitude}";
    }
}