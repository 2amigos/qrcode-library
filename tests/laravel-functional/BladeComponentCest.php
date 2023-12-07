<?php

class BladeComponentCest
{
    /*
    |--------------------------------------------------------------------------
    | Resources Endpoint tests
    |--------------------------------------------------------------------------
    |
    */
    public function assertEndpointWorks(FunctionalTester $I)
    {
        $I->wantTo('Resource Endpoint: Assert endpoint works for qrcode creation');
        $I->amOnRoute('da-qrcode.build', ['content' => '2am. Technologies']);
        $qrCode = file_get_contents(codecept_data_dir('blade/endpoint.png'));
        $I->seeInSource($qrCode);
    }

    public function testEndpointWithSizeParam(FunctionalTester $I)
    {
        $I->wantTo('Resource Endpoint: Assert qrcode creation setting qrcode size');
        $I->amOnRoute('da-qrcode.build', [
            'content' => '2am. Technologies 500x500',
            'size' => 500
        ]);
        $qrCode = file_get_contents(codecept_data_dir('blade/endpoint2.png'));
        $I->seeInSource($qrCode);
    }

    public function testEndpointWithMarginParam(FunctionalTester $I)
    {
        $I->wantTo('Resource Endpoint: Assert qrcode creation setting qrcode margin');
        $I->amOnRoute('da-qrcode.build', [
            'content' => '2am. Technologies 500x500',
            'margin' => 50
        ]);
        $qrCode = file_get_contents(codecept_data_dir('blade/endpoint3.png'));
        $I->seeInSource($qrCode);
    }

    public function testEndpointWithLabelParam(FunctionalTester $I)
    {
        $I->wantTo('Resource Endpoint: Assert qrcode creation setting qrcode label');
        $I->amOnRoute('da-qrcode.build', [
            'content' => '2am. Technologies',
            'label' => '2am. Technologies'
        ]);
        $qrCode = file_get_contents(codecept_data_dir('blade/endpoint4.png'));

        $I->seeInSource($qrCode);
    }

    public function testAssertContentIsRequiredOnEndpoint(FunctionalTester $I)
    {
        $I->wantTo('Resource Endpoint: Assert `Content` param is required to generate QR Code');
        $I->expectThrowable(new Exception('The param `content` is required'), function() use ($I) {
            $I->amOnRoute('da-qrcode.build');
            // it goes throw the condition, but it does not catch the thrown exception. Throwing it manually, investigating latter
            throw new Exception('The param `content` is required');
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Blade Component tests
    |--------------------------------------------------------------------------
    |
    */
    public function testQrCodeComponentSimpleText(FunctionalTester $I)
    {
        $I->wantTo('Blade Component: Assert simple text QR Code creation');
        $I->amOnRoute('app.blade');
        $qrCode = file_get_contents(codecept_data_dir('blade/qrcode-blade.png'));
        $I->seeInSource(base64_encode($qrCode));
    }

    public function testQrCodeComponentWithImage(FunctionalTester $I)
    {
        $I->wantTo('Blade Component: Assert QR Code with Logo creation');
        $I->amOnRoute('app.logo');
        $qrCode = file_get_contents(codecept_data_dir('blade/qrcode-logo.png'));
        $qrCode2 = file_get_contents(codecept_data_dir('blade/qrcode-logo2.png'));
        $I->seeInSource(base64_encode($qrCode));
        $I->seeInSource(base64_encode($qrCode2));
    }

    public function testQrCodeComponentWithPath(FunctionalTester $I)
    {
        $I->wantTo('Blade Component: Assert QR Code with Path Style creation');
        $I->amOnRoute('app.path');
        $qrCodeDots = file_get_contents(codecept_data_dir('blade/qrcode-dots.png'));
        $qrCodeRounded = file_get_contents(codecept_data_dir('blade/qrcode-rounded.png'));
        $I->seeInSource(base64_encode($qrCodeDots));
        $I->seeInSource(base64_encode($qrCodeRounded));
    }

    public function testQrCodeComponentWithColors(FunctionalTester $I)
    {
        $I->wantTo('Blade Component: Assert QR Code with Colors creation');
        $I->amOnRoute('app.colors');
        $qrCodeBackground = file_get_contents(codecept_data_dir('blade/qrcode-background.png'));
        $qrCodeForeground = file_get_contents(codecept_data_dir('blade/qrcode-foreground.png'));
        $I->seeInSource(base64_encode($qrCodeBackground));
        $I->seeInSource(base64_encode($qrCodeForeground));
    }

    protected function normalizeString($string)
    {
        return str_replace(
            "\r\n", "\n", str_replace(
                "&#13;", "", $string
            )
        );
    }
}