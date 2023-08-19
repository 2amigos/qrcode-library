<?php

/*
 * This file is part of the 2amigos/qrcode-library project.
 *
 * (c) 2amigOS! <http://2am.tech/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\QrCode\Contracts;

interface WriterInterface
{
    /**
     * @param QrCodeInterface $qrCode
     *
     * @return string
     */
    public function writeString(QrCodeInterface $qrCode): string;

    /**
     * @param QrCodeInterface $qrCode
     *
     * @return string
     */
    public function writeDataUri(QrCodeInterface $qrCode): string;

    /**
     * @param QrCodeInterface $qrCode
     * @param string          $path
     *
     * @return bool|int the number of bytes that were written to the file, or false on failure.
     */
    public function writeFile(QrCodeInterface $qrCode, string $path);

    /**
     * @return string
     */
    public function getContentType(): string;

    /**
     * @return string
     */
    public function getName(): string;
}
