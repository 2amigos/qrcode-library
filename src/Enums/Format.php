<?php

/*
 * This file is part of the 2amigos/qrcode-library project.
 *
 * (c) 2amigOS! <http://2am.tech/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\QrCode\Enums;

use Da\QrCode\Format\BookMarkFormat;
use Da\QrCode\Format\BtcFormat;
use Da\QrCode\Format\GeoFormat;
use Da\QrCode\Format\ICalFormat;
use Da\QrCode\Format\MailMessageFormat;
use Da\QrCode\Format\MailToFormat;
use Da\QrCode\Format\MeCardFormat;
use Da\QrCode\Format\MmsFormat;
use Da\QrCode\Format\PhoneFormat;
use Da\QrCode\Format\SmsFormat;
use Da\QrCode\Format\VCardFormat;
use Da\QrCode\Format\WifiFormat;
use Da\QrCode\Format\YoutubeFormat;

final class Format
{
    public const TEXT = 'text';
    public const BOOK_MARK = BookMarkFormat::class;
    public const BTC = BtcFormat::class;
    public const GEO = GeoFormat::class;
    public const I_CAL = ICalFormat::class;
    public const MAIL_MESSAGE = MailMessageFormat::class;
    public const MAIL_TO = MailToFormat::class;
    public const ME_CARD = MeCardFormat::class;
    public const MMS = MmsFormat::class;
    public const PHONE_FORMAT = PhoneFormat::class;
    public const SNS_FORMAT = SmsFormat::class;
    public const V_CARD = VCardFormat::class;
    public const WIFI = WifiFormat::class;
    public const YOUTUBE = YoutubeFormat::class;
}
