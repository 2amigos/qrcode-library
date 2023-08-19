<?php

namespace app\controllers;

use Da\QrCode\Action\QrCodeAction;
use Da\QrCode\Format\MailToFormat;
use yii\web\Controller;
use yii\web\Response;

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

    public function actionComponent()
    {
        $qr = \Yii::$app->get('qr');

        \Yii::$app->response->format = Response::FORMAT_RAW;
        \Yii::$app->response->headers->add('Content-Type', $qr->getContentType());

        return $qr
            ->setText('https://2am.tech')
            ->setLabel('2amigos consulting group llc')
            ->setBackgroundColor(0, 0, 0)
            ->setForegroundColor(255, 255, 255)
            ->writeString();
    }

    public function actionIndex()
    {
        return $this->render('index');
    }
}
