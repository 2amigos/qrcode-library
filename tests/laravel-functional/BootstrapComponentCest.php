<?php

class BootstrapComponentCest
{
    public function assertEndpointWorks(FunctionalTester $I)
    {
        $I->wantTo('Assert endpoint works for qrcode creation');
        $I->amOnRoute('da-qrcode.build', ['content' => '2am. Technologies']);

        $qrCode = file_get_contents(codecept_data_dir('blade/endpoint.png'));
        $I->seeInSource($qrCode);

        $I->amOnRoute('da-qrcode.build', [
            'content' => '2am. Technologies 500x500',
            'size' => 500
        ]);

        $qrCode = file_get_contents(codecept_data_dir('blade/endpoint2.png'));
        $I->seeInSource($qrCode);

        $I->amOnRoute('da-qrcode.build', [
            'content' => '2am. Technologies 500x500',
            'margin' => 50
        ]);
        $source = $I->grabPageSource();
        file_put_contents(codecept_data_dir('blade/endpoint3.png'), $source);
        $qrCode = file_get_contents(codecept_data_dir('blade/endpoint3.png'));
        $I->seeInSource($qrCode);
    }
}