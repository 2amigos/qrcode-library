<?php
/**
 * @copyright Copyright (c) 2013-15 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\lib;

    /**
     * Class Rs
     *
     * Based on libqrencode C library distributed under LGPL 2.1
     * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
     *
     * @author Antonio Ramirez <amigo.cobos@gmail.com>
     * @link http://www.ramirezcobos.com/
     * @link http://www.2amigos.us/
     * @package dosamigos\qrcode\lib
     */
/**
 * Class Rs
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\lib
 */
class Rs
{

    /**
     * @var array
     */
    public static $items = [];

    /**
     * @param $symSize
     * @param $gfPoly
     * @param $fcr
     * @param $prim
     * @param $nRoots
     * @param $pad
     *
     * @return RsItem|null
     */
    public static function initRs($symSize, $gfPoly, $fcr, $prim, $nRoots, $pad)
    {
        foreach (static::$items as $rs) {
            if ($rs->pad != $pad)
                continue;
            if ($rs->nRoots != $nRoots)
                continue;
            if ($rs->mm != $symSize)
                continue;
            if ($rs->gfPoly != $gfPoly)
                continue;
            if ($rs->fcr != $fcr)
                continue;
            if ($rs->prim != $prim)
                continue;

            return $rs;
        }

        $rs = RsItem::initRsChar($symSize, $gfPoly, $fcr, $prim, $nRoots, $pad);
        array_unshift(static::$items, $rs);

        return $rs;
    }

}