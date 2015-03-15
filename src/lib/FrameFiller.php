<?php
/**
 * @copyright Copyright (c) 2013-15 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\lib;

use yii\base\Object;

/**
 * Class FrameFiller
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\lib
 */
class FrameFiller extends Object
{
    public $width;
    public $frame;
    public $x;
    public $y;
    public $dir;
    public $bit;

    /**
     * @param array $width
     * @param $frame
     */
    public function __construct($width, &$frame)
    {
        $this->width = $width;
        $this->frame = $frame;
        $this->x = $width - 1;
        $this->y = $width - 1;
        $this->dir = -1;
        $this->bit = -1;
    }

    /**
     * @param $at
     * @param $val
     */
    public function setFrameAt($at, $val)
    {
        $this->frame[$at['y']][$at['x']] = chr($val);
    }

    /**
     * @param $at
     *
     * @return int
     */
    public function getFrameAt($at)
    {
        return ord($this->frame[$at['y']][$at['x']]);
    }

    /**
     * @return array|null
     */
    public function next()
    {
        do {

            if ($this->bit == -1) {
                $this->bit = 0;
                return ['x' => $this->x, 'y' => $this->y];
            }

            $x = $this->x;
            $y = $this->y;
            $w = $this->width;

            if ($this->bit == 0) {
                $x--;
                $this->bit++;
            } else {
                $x++;
                $y += $this->dir;
                $this->bit--;
            }

            if ($this->dir < 0) {
                if ($y < 0) {
                    $y = 0;
                    $x -= 2;
                    $this->dir = 1;
                    if ($x == 6) {
                        $x--;
                        $y = 9;
                    }
                }
            } else {
                if ($y == $w) {
                    $y = $w - 1;
                    $x -= 2;
                    $this->dir = -1;
                    if ($x == 6) {
                        $x--;
                        $y -= 8;
                    }
                }
            }
            if ($x < 0 || $y < 0)
                return null;

            $this->x = $x;
            $this->y = $y;
        } while (ord($this->frame[$y][$x]) & 0x80);

        return ['x' => $x, 'y' => $y];
    }
}
