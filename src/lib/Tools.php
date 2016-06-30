<?php
/**
 * @copyright Copyright (c) 2013-15 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\lib;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class Tools
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\lib
 */
class Tools
{

    /**
     * @param $frame
     *
     * @return mixed
     */
    public static function binarize($frame)
    {
        $len = count($frame);
        foreach ($frame as &$frameLine) {

            for ($i = 0; $i < $len; $i++) {
                $frameLine[$i] = (ord($frameLine[$i]) & 1) ? '1' : '0';
            }
        }

        return $frame;
    }


    /**
     * Clears cache
     */
    public static function clearCache()
    {
        Specifications::$frames = [];
    }


    /**
     * Important!
     * Make sure the cache directory is writable
     */
    public static function buildCache()
    {
        static::markTime('before_build_cache');

        $mask = new Mask();
        for ($a = 1; $a <= Enum::QRSPEC_VERSION_MAX; $a++) {
            $frame = Specifications::newFrame($a);

            $cache_dir = static::getCacheDir();
            $fileName = $cache_dir . 'frame_' . $a . '.png';
            Image::png(static::binarize($frame), $fileName, 1, 0);

            $width = count($frame);
            $bitMask = array_fill(0, $width, array_fill(0, $width, 0));
            for ($maskNo = 0; $maskNo < 8; $maskNo++) {
                $mask->makeMaskNo($maskNo, $width, $frame, $bitMask, true);
            }
        }

        static::markTime('after_build_cache');
    }


    /**
     * @param $frame
     */
    public static function dumpMask($frame)
    {
        $width = count($frame);
        for ($y = 0; $y < $width; $y++) {
            for ($x = 0; $x < $width; $x++) {
                echo ord($frame[$y][$x]) . ',';
            }
        }
    }


    /**
     * @param $markerId
     */
    public static function markTime($markerId)
    {
    	if (Yii::$app->getRequest()->isConsoleRequest) {
            return false;
    	}
        list($usec, $sec) = explode(" ", microtime());
        $time = ((float)$usec + (float)$sec);

        $qr_time_bench = Yii::$app->session->get('qr_time_bench', []);

        $qr_time_bench[$markerId] = $time;

	Yii::$app->session->set('qr_time_bench', $qr_time_bench);
    }


    /**
     * Shows time benchmark
     */
    public static function timeBenchmark()
    {
    	if (Yii::$app->getRequest()->isConsoleRequest) {
            return false;
    	}
        static::markTime('finish');

        $lastTime = 0;
        $startTime = 0;
        $p = 0;

        echo '<table cellpadding="3" cellspacing="1">
		<thead><tr style="border-bottom:1px solid silver"><td colspan="2" style="text-align:center">BENCHMARK</td></tr></thead>
		<tbody>';

        $qr_time_bench = Yii::$app->session->get('qr_time_bench', []);

        foreach ($qr_time_bench as $markerId => $thisTime) {
            if ($p > 0)
                echo '<tr><th style="text-align:right">till ' . $markerId . ': </th><td>' . number_format(
                        $thisTime - $lastTime,
                        6
                    ) . 's</td></tr>';
            else
                $startTime = $thisTime;
            $p++;
            $lastTime = $thisTime;
        }

        echo '</tbody><tfoot>
		<tr style="border-top:2px solid black"><th style="text-align:right">TOTAL: </th><td>' . number_format(
                $lastTime - $startTime,
                6
            ) . 's</td></tr>
		</tfoot>
		</table>';
    }

    /**
     * Returns the cache dir
     * @return mixed
     */
    public static function getCacheDir()
    {
        $path[] = __DIR__;
        $path[] = '..';
        $path[] = 'cache';

        return ArrayHelper::getValue(
            Yii::$app->params,
            'qrCacheDir',
            implode(DIRECTORY_SEPARATOR, $path) . DIRECTORY_SEPARATOR
        );
    }
}
