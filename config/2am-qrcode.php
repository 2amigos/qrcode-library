
<?php
return [

    /*
    |--------------------------------------------------------------------------
    | Component Prefix
    |--------------------------------------------------------------------------
    |
    | Defines the prefix for the component
    |
    */
    'prefix' => '2am',

    /*
    |--------------------------------------------------------------------------
    | QR Code Size
    |--------------------------------------------------------------------------
    |
    | Defines the default size for the generated qrcode.
    |
    */
    'size' => 300,

    /*
    |--------------------------------------------------------------------------
    | QR Code Margin
    |--------------------------------------------------------------------------
    |
    | Defines the default margin for the generated qrcode.
    |
    */
    'margin' => 15,

    /*
    |--------------------------------------------------------------------------
    | QR Code Logo
    |--------------------------------------------------------------------------
    |
    | Defines the default logo path for the generated qrcode.
    | A full path should be provided.
    | By default, the logo image will be resized to be square shaped, you can change this
    | behavior by setting scaleLogoHeight to true
    |
    */
    'logoPath' => null,

    /*
    |--------------------------------------------------------------------------
    | QR Scale Logo Height
    |--------------------------------------------------------------------------
    |
    */
    'scaleLogoHeight' => false,

    /*
    |--------------------------------------------------------------------------
    | QR Logo Size
    |--------------------------------------------------------------------------
    |
    | Defines the default logo size for the generated qrcode.
    | The suggested size is the 16% of the QR Code width
    |
    */
    'logoSize' => null,

    /*
    |--------------------------------------------------------------------------
    | QR Code Size
    |--------------------------------------------------------------------------
    |
    | Defines the default size for the generated qrcode.
    |
    */
    'background' => [
        'r' => 255,
        'g' => 255,
        'b' => 255,
    ],

    /*
    |--------------------------------------------------------------------------
    | QR Code Size
    |--------------------------------------------------------------------------
    |
    | Defines the default size for the generated qrcode.
    |
    */
    'foreground' => [
        'r' => 0,
        'g' => 0,
        'b' => 0,
        'a' => 100,
    ],

    /*
     * ------------------------------------------------------------------------
     * QR Code Label Style
     *
     * Defines the default style for the QR Code label style
     * ------------------------------------------------------------------------
     */
    'label' => [
        'fontPath' => null,
        'size' => 16,
        'align' => \Da\QrCode\Enums\Label::ALIGN_CENTER,
    ]
];
