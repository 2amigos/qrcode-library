<?php

/*
 * This file is part of the 2amigos/qrcode-library project.
 *
 * (c) 2amigOS! <http://2am.tech/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\QrCode\Factory;

use Da\QrCode\Contracts\WriterInterface;
use Da\QrCode\Exception\UnknownWriterException;
use Da\QrCode\Writer\EpsWriter;
use Da\QrCode\Writer\JpgWriter;
use Da\QrCode\Writer\PngWriter;
use Da\QrCode\Writer\SvgWriter;

class WriterFactory
{
    /**
     * @param string $name
     *
     * @throws UnknownWriterException
     * @return WriterInterface
     */
    public static function fromName(string $name): WriterInterface
    {
        $writerMap = [
            'eps' => EpsWriter::class,
            'jpg' => JpgWriter::class,
            'png' => PngWriter::class,
            'svg' => SvgWriter::class
        ];

        if (!array_key_exists($name, $writerMap)) {
            throw new UnknownWriterException(sprintf('Unknown writer name "%s"', $name));
        }

        return new $writerMap[$name]();
    }
}
