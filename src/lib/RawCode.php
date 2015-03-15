<?php
/**
 * @copyright Copyright (c) 2013-15 2amigOS! Consulting Group LLC
 * @link http://2amigos.us
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 */
namespace dosamigos\qrcode\lib;

use yii\base\InvalidConfigException;

/**
 * Class RawCode
 *
 * Based on libqrencode C library distributed under LGPL 2.1
 * Copyright (C) 2006, 2007, 2008, 2009 Kentaro Fukuchi <fukuchi@megaui.net>
 *
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 * @package dosamigos\qrcode\lib
 */
class RawCode
{
    /**
     * @var int
     */
    public $version;
    /**
     * @var array|null
     */
    public $dataCode = [];
    /**
     * @var array error correction codes
     */
    public $eccCode = [];
    /**
     * @var array blocks
     */
    public $blocks;
    /**
     * @var Block[]
     */
    public $rsBlocks = [];
    /**
     * @var int
     */
    public $count;
    /**
     * @var int data length
     */
    public $dataLength;
    /**
     * @var int error correction length
     */
    public $eccLength;
    /**
     * @var
     */
    public $b1;


    /**
     * @param Input $input
     */
    public function __construct(Input $input)
    {
        $spec = array(0, 0, 0, 0, 0);

        $this->dataCode = $input->getByteStream();
        if (is_null($this->dataCode)) {
            throw new InvalidConfigException('null input string');
        }

        Specifications::getEccSpec($input->getVersion(), $input->getErrorCorrectionLevel(), $spec);

        $this->version = $input->getVersion();
        $this->b1 = Specifications::rsBlockNum1($spec);
        $this->dataLength = Specifications::rsDataLength($spec);
        $this->eccLength = Specifications::rsEccLength($spec);
        $this->eccCode = array_fill(0, $this->eccLength, 0);
        $this->blocks = Specifications::rsBlockNum($spec);

        $ret = $this->init($spec);
        if ($ret < 0) {
            throw new \Exception('block alloc error');
        }

        $this->count = 0;
    }


    /**
     * @param array $spec
     *
     * @return int
     */
    public function init(array $spec)
    {
        $dl = Specifications::rsDataCodes1($spec);
        $el = Specifications::rsEccCodes1($spec);
        $rs = Rs::initRs(8, 0x11d, 0, 1, $el, 255 - $dl - $el);

        $blockNo = 0;
        $dataPos = 0;
        $eccPos = 0;
        for ($i = 0; $i < Specifications::rsBlockNum1($spec); $i++) {
            $ecc = array_slice($this->eccCode, $eccPos);
            $this->rsBlocks[$blockNo] = new Block($dl, array_slice($this->dataCode, $dataPos), $el, $ecc, $rs);
            $this->eccCode = array_merge(array_slice($this->eccCode, 0, $eccPos), $ecc);

            $dataPos += $dl;
            $eccPos += $el;
            $blockNo++;
        }

        if (Specifications::rsBlockNum2($spec) == 0)
            return 0;

        $dl = Specifications::rsDataCodes2($spec);
        $el = Specifications::rsEccCodes2($spec);
        $rs = Rs::initRs(8, 0x11d, 0, 1, $el, 255 - $dl - $el);

        if ($rs == null) return -1;

        for ($i = 0; $i < Specifications::rsBlockNum2($spec); $i++) {
            $ecc = array_slice($this->eccCode, $eccPos);
            $this->rsBlocks[$blockNo] = new Block($dl, array_slice($this->dataCode, $dataPos), $el, $ecc, $rs);
            $this->eccCode = array_merge(array_slice($this->eccCode, 0, $eccPos), $ecc);

            $dataPos += $dl;
            $eccPos += $el;
            $blockNo++;
        }

        return 0;
    }


    /**
     * @return int
     */
    public function getCode()
    {

        if ($this->count < $this->dataLength) {
            $row = $this->count % $this->blocks;
            $col = (int)($this->count / $this->blocks);
            if ($col >= $this->rsBlocks[0]->dataLength) {
                $row += $this->b1;
            }
            $ret = $this->rsBlocks[$row]->data[$col];
        } else if ($this->count < $this->dataLength + $this->eccLength) {
            $row = ($this->count - $this->dataLength) % $this->blocks;
            $col = (int)(($this->count - $this->dataLength) / $this->blocks);
            $ret = $this->rsBlocks[$row]->ecc[$col];
        } else {
            return 0;
        }
        $this->count++;

        return $ret;
    }
}