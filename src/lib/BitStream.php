<?php
/**
 * @copyright Copyright (c) 2013-15 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\lib;

/**
 * Class BitStream
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\lib
 */
class BitStream
{
    /**
     * @var array the stream data
     */
    public $data = [];

    /**
     * @return int
     */
    public function size()
    {
        return count($this->data);
    }

    /**
     * @param $length
     *
     * @return int
     */
    public function allocate($length)
    {
        $this->data = array_fill(0, $length, 0);
        return 0;
    }

    /**
     * @param BitStream $arg
     *
     * @return int
     */
    public function append(BitStream $arg)
    {
        if (is_null($arg)) {
            return -1;
        }

        if ($arg->size() == 0) {
            return 0;
        }

        if ($this->size() == 0) {
            $this->data = $arg->data;
            return 0;
        }

        $this->data = array_values(array_merge($this->data, $arg->data));

        return 0;
    }

    /**
     * @param int $bits
     * @param mixed $num
     *
     * @return int
     */
    public function appendNum($bits, $num)
    {
        if ($bits == 0)
            return 0;

        $b = BitStream::newFromNum($bits, $num);

        if (is_null($b))
            return -1;

        $ret = $this->append($b);
        unset($b);

        return $ret;
    }

    /**
     * @param int $size
     * @param array $data
     *
     * @return int
     */
    public function appendBytes($size, $data)
    {
        if ($size == 0)
            return 0;

        $b = BitStream::newFromBytes($size, $data);

        if (is_null($b))
            return -1;

        $ret = $this->append($b);
        unset($b);

        return $ret;
    }

    /**
     * @return array
     */
    public function toByte()
    {

        $size = $this->size();

        if ($size == 0)
            return [];

        $data = array_fill(0, (int)(($size + 7) / 8), 0);
        $bytes = (int)($size / 8);

        $p = 0;

        for ($i = 0; $i < $bytes; $i++) {
            $v = 0;
            for ($j = 0; $j < 8; $j++) {
                $v = $v << 1;
                $v |= $this->data[$p];
                $p++;
            }
            $data[$i] = $v;
        }

        if ($size & 7) {
            $v = 0;
            for ($j = 0; $j < ($size & 7); $j++) {
                $v = $v << 1;
                $v |= $this->data[$p];
                $p++;
            }
            $data[$bytes] = $v;
        }

        return $data;
    }

    /**
     * @param int $bits
     * @param mixed $num
     *
     * @return BitStream
     */
    public static function newFromNum($bits, $num)
    {
        $bStream = new BitStream();
        $bStream->allocate($bits);

        $mask = 1 << ($bits - 1);
        for ($i = 0; $i < $bits; $i++) {
            $bStream->data[$i] = ($num & $mask) ? 1 : 0;
            $mask = $mask >> 1;
        }

        return $bStream;
    }

    /**
     * @param int $size
     * @param array $data
     *
     * @return BitStream
     */
    public static function newFromBytes($size, $data)
    {
        $bStream = new BitStream();
        $bStream->allocate($size * 8);
        $p = 0;

        for ($i = 0; $i < $size; $i++) {
            $mask = 0x80;
            for ($j = 0; $j < 8; $j++) {
                $bStream->data[$i] = ($data[$i] & $mask) ? 1 : 0;
                $p++;
                $mask = $mask >> 1;
            }
        }

        return $bStream;
    }
}