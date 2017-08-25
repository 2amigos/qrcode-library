<?php

namespace app\controllers;

use Da\QrCode\Action\QrCodeAction;
use Da\QrCode\Format\MailToFormat;
use yii\web\Controller;

class SiteController extends Controller
{
    public function actions()
    {
        return [
            'qr' => [
                'class' => QrCodeAction::className(),
                'text' => (new MailToFormat(['email' => 'hola@2amigos.us']))
            ]
        ];
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
}
