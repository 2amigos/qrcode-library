<?php


class QrCodeActionCest
{
    // tests
    public function qrCodeWorks(FunctionalTester $I)
    {
        $I->wantTo('Ensure that QrCodeAction and QrComponent works.');
        $I->amGoingTo('Call the configured action "qr" and should receive the "png" image.');
        $I->amOnRoute('/site/qr');
        $source = $I->grabPageSource();

        $I->openFile(codecept_data_dir('data-action.png'));
        $I->seeInThisFile($source);
    }

    // tests
    public function qrCodeComponent(FunctionalTester $I)
    {
        $I->wantTo('Ensure that QrCodeComponents works');
        $I->amGoingTo('Call the route with component defined to respond with qrcode mimetype');
        $I->amOnRoute('/site/component');
        $source = $I->grabPageSource();
        #file_put_contents(codecept_data_dir('data-component.png'), $source);die;
        $I->openFile(codecept_data_dir('data-component.png'));
        $I->seeInThisFile($source);
    }
}
