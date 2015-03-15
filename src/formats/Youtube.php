<?php
/**
 * @copyright Copyright (c) 2013-15 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\formats;

/**
 * Class Youtube formats a string to a valid youtube video link
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\formats
 */
class Youtube extends FormatAbstract
{
    /**
     * @var string the video ID
     */
    public $videoId;

    /**
     * @return string the formatted string to be encoded
     */
    public function getText()
    {
        return "youtube://{$this->videoId}";
    }

}