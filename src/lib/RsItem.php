<?php
/**
 * @copyright Copyright (c) 2013-15 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\lib;

/**
 * Class RsItem
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\lib
 */
class RsItem
{

    /**
     * @var int bits per symbol
     */
    public $mm;
    /**
     * @var int symbols per block  (= (1<<mm)-1)
     */
    public $nn;
    /**
     * @var array log lookup table
     */
    public $alphaTo = [];
    /**
     * @var array antilog lookup table
     */
    public $indexOf = [];
    /**
     * @var array polynomial generator
     */
    public $genPoly = [];
    /**
     * @var int number of generator roots = number of parity symbols
     */
    public $nRoots;
    /**
     * @var mixed first consecutive root, index form
     */
    public $fcr;
    /**
     * @var mixed primitive element, index form
     */
    public $prim;
    /**
     * @var mixed prim-th root of 1, index form
     */
    public $iPrim;
    /**
     * @var int pending bytes in shortened block
     */
    public $pad;
    /**
     * @var array
     */
    public $gfPoly;


    /**
     * @param $x
     *
     * @return int
     */
    public function modnn($x)
    {
        while ($x >= $this->nn) {
            $x -= $this->nn;
            $x = ($x >> $this->mm) + ($x & $this->nn);
        }

        return $x;
    }


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
    public static function initRsChar($symSize, $gfPoly, $fcr, $prim, $nRoots, $pad)
    {
        // Common code for intializing a Reed-Solomon control block (char or int symbols)
        // Copyright 2004 Phil Karn, KA9Q
        // May be used under the terms of the GNU Lesser General Public License (LGPL)

        $rs = null;

        // Check parameter ranges
        if ($symSize < 0 || $symSize > 8)
            return $rs;
        if ($fcr < 0 || $fcr >= (1 << $symSize))
            return $rs;
        if ($prim <= 0 || $prim >= (1 << $symSize))
            return $rs;
        if ($nRoots < 0 || $nRoots >= (1 << $symSize))
            return $rs; // Can't have more roots than symbol values!
        if ($pad < 0 || $pad >= ((1 << $symSize) - 1 - $nRoots))
            return $rs; // Too much padding

        $rs = new RsItem();
        $rs->mm = $symSize;
        $rs->nn = (1 << $symSize) - 1;
        $rs->pad = $pad;

        $rs->alphaTo = array_fill(0, $rs->nn + 1, 0);
        $rs->indexOf = array_fill(0, $rs->nn + 1, 0);

        // PHP style macro replacement ;)
        $nn = &$rs->nn;
        $a0 = &$nn;

        // Generate Galois field lookup tables
        $rs->indexOf[0] = $a0; // log(zero) = -inf
        $rs->alphaTo[$a0] = 0; // alpha**-inf = 0
        $sr = 1;

        for ($i = 0; $i < $rs->nn; $i++) {
            $rs->indexOf[$sr] = $i;
            $rs->alphaTo[$i] = $sr;
            $sr <<= 1;
            if ($sr & (1 << $symSize))
                $sr ^= $gfPoly;

            $sr &= $rs->nn;
        }

        if ($sr != 1) {
            // field generator polynomial is not primitive!
            $rs = null;
            return $rs;
        }

        /* Form RS code generator polynomial from its roots */
        $rs->genPoly = array_fill(0, $nRoots + 1, 0);

        $rs->fcr = $fcr;
        $rs->prim = $prim;
        $rs->nRoots = $nRoots;
        $rs->gfPoly = $gfPoly;

        /* Find prim-th root of 1, used in decoding */
        for ($iPrim = 1; ($iPrim % $prim) != 0; $iPrim += $rs->nn) {
            ;
        } // intentional empty-body loop!

        $rs->iPrim = (int)($iPrim / $prim);
        $rs->genPoly[0] = 1;

        for ($i = 0, $root = $fcr * $prim; $i < $nRoots; $i++, $root += $prim) {
            $rs->genPoly[$i + 1] = 1;

            // Multiply rs->genPoly[] by  @**(root + x)
            for ($j = $i; $j > 0; $j--) {
                if ($rs->genPoly[$j] != 0)
                    $rs->genPoly[$j] = $rs->genPoly[$j - 1] ^ $rs->alphaTo[$rs->modnn(
                            $rs->indexOf[$rs->genPoly[$j]] + $root
                        )];
                else
                    $rs->genPoly[$j] = $rs->genPoly[$j - 1];
            }
            // rs->genPoly[0] can never be zero
            $rs->genPoly[0] = $rs->alphaTo[$rs->modnn($rs->indexOf[$rs->genPoly[0]] + $root)];
        }

        // convert rs->genPoly[] to index form for quicker encoding
        for ($i = 0; $i <= $nRoots; $i++) {
            $rs->genPoly[$i] = $rs->indexOf[$rs->genPoly[$i]];
        }

        return $rs;
    }


    /**
     * @param $data
     * @param $parity
     */
    public function encodeRsChar($data, &$parity)
    {
        $nn = &$this->nn;
        $alphaTo = &$this->alphaTo;
        $indexOf = &$this->indexOf;
        $genPoly = &$this->genPoly;
        $nRoots = &$this->nRoots;
        $pad = &$this->pad;
        $aO = &$nn;

        $parity = array_fill(0, $nRoots, 0);

        for ($i = 0; $i < ($nn - $nRoots - $pad); $i++) {

            $feedback = $indexOf[$data[$i] ^ $parity[0]];
            if ($feedback != $aO) {
                // feedback term is non-zero
                // This line is unnecessary when $genPoly[$nRoots] is unity, as it must
                // always be for the polynomials constructed by initRs()
                $feedback = $this->modnn($nn - $genPoly[$nRoots] + $feedback);

                for ($j = 1; $j < $nRoots; $j++) {
                    $parity[$j] ^= $alphaTo[$this->modnn($feedback + $genPoly[$nRoots - $j])];
                }
            }

            // Shift
            array_shift($parity);
            if ($feedback != $aO)
                array_push($parity, $alphaTo[$this->modnn($feedback + $genPoly[0])]);
            else
                array_push($parity, 0);
        }
    }

}