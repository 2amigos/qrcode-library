<?php
/**
 * @copyright Copyright (c) 2013-15 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\formats;

use dosamigos\qrcode\traits\UrlTrait;
use yii\base\InvalidConfigException;
use yii\validators\UrlValidator;

/**
 * Class BookMark formats a string to properly create a Bookmark QrCode
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\formats
 */
class BookMark extends FormatAbstract
{
    use UrlTrait;

    /**
     * @var string bookmark title
     */
    public $title;

    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException
     */
    public function init()
    {
        if ($this->url === null) {
            throw new InvalidConfigException("'url' cannot be empty.");
        }
    }

    /**
     * @inheritdoc
     */
    public function getText()
    {
        return "MEBKM:TITLE:{$this->title};URL:{$this->url};;";
    }
}