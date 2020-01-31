<?php

/*
 * This file is part of the 2amigos/qrcode-library project.
 *
 * (c) 2amigOS! <http://2amigos.us/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\QrCode\Writer;

use BaconQrCode\Renderer\Image\Png;
use Da\QrCode\Traits\ImageTrait;

class PngWriter extends AbstractWriter
{
    use ImageTrait;

    /**
     * PngWriter constructor.
     */
    public function __construct()
    {
        parent::__construct(new Png());
    }

    /**
     * @inheritdoc
     */
    public function getContentType(): string
    {
        return 'image/png';
    }

    /**
     * @param resource $image
     *
     * @return string
     */
    protected function imageToString($image): string
    {
        ob_start();
        imagepng($image);

        return ob_get_clean();
    }
}
