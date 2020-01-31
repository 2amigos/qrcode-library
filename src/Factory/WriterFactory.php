<?php

/*
 * This file is part of the 2amigos/qrcode-library project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
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
    protected static $map = [
        'eps' => EpsWriter::class,
        'jpg' => JpgWriter::class,
        'png' => PngWriter::class,
        'svg' => SvgWriter::class
    ];

    /**
     * @param string $name
     *
     * @throws UnknownWriterException
     * @return WriterInterface
     */
    public static function fromName(string $name): WriterInterface
    {
        if (!array_key_exists($name, self::$map)) {
            throw new UnknownWriterException(sprintf('Unknown writer name "%s"', $name));
        }

        return new self::$map[$name];
    }
}
