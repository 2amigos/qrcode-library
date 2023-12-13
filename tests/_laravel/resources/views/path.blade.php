<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body class="antialiased">
        <x-2am-qrcode
            :content="'2am. Technologies'"
            :pathStyle="\Da\QrCode\Enums\Path::DOTS"
            :intensity="0.9"
        />

        <x-2am-qrcode
            :content="'2am. Technologies'"
            :pathStyle="\Da\QrCode\Enums\Path::ROUNDED"
            :intensity="0.9"
        />
    </body>
</html>
