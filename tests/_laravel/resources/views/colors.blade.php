<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body class="antialiased">
        @php
        $background = [
            'r' => 255,
            'g' => 10,
            'b' => 10,
        ];

        $foreground = [
            'r' => 255,
            'g' => 0,
            'b' => 0,
        ];

        $content = [
            'title' => '2am. Technologies',
            'url' => 'https://2am.tech',
        ];
        @endphp
        <x-2am-qrcode
            :content="$content"
            :format="\Da\QrCode\Enums\Format::BOOK_MARK"
            :background="$background"
        />

        <x-2am-qrcode
            :content="'2am. Technologies'"
            :foreground="$background"
        />
    </body>
</html>
