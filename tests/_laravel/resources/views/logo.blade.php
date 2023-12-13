<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
    </head>
    <body class="antialiased">
        @php $logoPath = public_path('logo.png'); @endphp
        <x-2am-qrcode
            :content="'2am. Technologies'"
            :logoPath="$logoPath"
            :logoSize="50"
        />

        @php
            $logoPath2 = public_path('logo2.png');
            $content = ['text' => '2am. Technologies'];
        @endphp
        <x-2am-qrcode
            :content="$content"
            :logoPath="$logoPath2"
            :logoSize="50"
            :scaleLogoHeight="true"
        />
    </body>
</html>
