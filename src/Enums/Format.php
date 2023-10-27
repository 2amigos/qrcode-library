<?php

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
use MabeEnum\Enum;

final class Format extends Enum
{
    const Text = 'text';
    const BookMark = BookMarkFormat::class;
    const Btc = BtcFormat::class;
    const Geo = GeoFormat::class;
    const ICal = ICalFormat::class;
    const MailMessage = MailMessageFormat::class;
    const MailTo = MailToFormat::class;
    const MeCard = MeCardFormat::class;
    const Mms = MmsFormat::class;
    const PhoneFormat = PhoneFormat::class;
    const SmsFormat = SmsFormat::class;
    const VCard = VCardFormat::class;
    const Wifi = WifiFormat::class;
    const Youtube = YoutubeFormat::class;
}