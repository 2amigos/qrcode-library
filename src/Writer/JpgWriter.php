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

use BaconQrCode\Renderer\Image\ImageBackEndInterface;
use Da\QrCode\Renderer\GdImageBackEnd;
use Da\QrCode\Traits\ImageTrait;

class JpgWriter extends AbstractWriter
{
    use ImageTrait;

    /**
     * JpgWriter constructor.
     *
     * Defaults to the pure-GD back end so no ImageMagick installation is required. Pass an
     * {@see ImageBackEndInterface} (e.g. `new ImagickImageBackEnd('jpeg')`) to opt into Imagick.
     */
    public function __construct(?ImageBackEndInterface $renderBackEnd = null)
    {
        parent::__construct($renderBackEnd ?? new GdImageBackEnd('jpeg'));
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
