<?php

return [
    'id' => 'yii2-qr-test-web',
    'basePath' => dirname(__DIR__),
    'language' => 'en-US',
    'aliases' => [
        '@tests' => dirname(dirname(__DIR__)),
        '@vendor' => VENDOR_DIR,
        '@bower' => VENDOR_DIR . '/bower',
    ],
    'components' => [
        'qr' => [
            'class' => '\Da\QrCode\Component\QrCodeComponent',
        ],
        'assetManager' => [
            'basePath' => __DIR__ . '/../assets',
        ],
        'mailer' => [
            'useFileTransport' => true,
        ],
        'urlManager' => [
            'showScriptName' => true,
        ],
        'request' => [
            'cookieValidationKey' => 'test',
            'enableCsrfValidation' => false,
        ]
    ],
    'params' => [],
];
