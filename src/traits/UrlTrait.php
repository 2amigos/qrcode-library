<?php
/**
 * @copyright Copyright (c) 2013-15 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\traits;


use yii\base\InvalidConfigException;
use yii\validators\UrlValidator;

trait UrlTrait
{
    /**
     * @var string a valid URL
     */
    protected $url;

    /**
     * @param string $value the URL
     *
     * @throws InvalidConfigException
     */
    public function setUrl($value)
    {
        $error = null;
        $validator = new UrlValidator();
        if (!$validator->validate($value, $error)) {
            throw new InvalidConfigException($error);
        }

        $this->url = $value;
    }

    /**
     * @return string the URL
     */
    public function getUrl() {
        return $this->url;
    }
}