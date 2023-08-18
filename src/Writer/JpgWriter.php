<?php

/*
 * This file is part of the 2amigos/qrcode-library project.
 *
 * (c) 2amigOS! <http://2am.tech/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\QrCode\Writer;

use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use Da\QrCode\Traits\ImageTrait;

class JpgWriter extends AbstractWriter
{
    use ImageTrait;

    /**
     * JpgWriter constructor.
     */
    public function __construct()
    {
        parent::__construct(new ImagickImageBackEnd('jpeg'));
    }

    /**
     * @inheritdoc
     */
    public function getContentType(): string
    {
        return 'image/jpeg';
    }

    /**
     * @param resource $image
     *
     * @return string
     */
    protected function imageToString($image): string
    {
        ob_start();
        imagejpeg($image);

        return ob_get_clean();
    }
}
