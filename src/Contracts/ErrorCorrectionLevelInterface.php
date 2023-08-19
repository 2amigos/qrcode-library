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

interface ErrorCorrectionLevelInterface
{
    public const LOW = 'low';
    public const MEDIUM = 'medium';
    public const QUARTILE = 'quartile';
    public const HIGH = 'high';
}
