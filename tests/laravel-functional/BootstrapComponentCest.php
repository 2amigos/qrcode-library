<?php

class BootstrapComponentCest
{
    // tests
    public function itWorks(FunctionalTester $I)
    {
        $I->wantTo('Assert endpoint works for qrcode creation');
        $I->amGoingTo('Call endpoint');
        $I->amOnRoute('/da-qrcode/build?content=2am. Technologies');
        $I->seeInSource('asd');
    }
}