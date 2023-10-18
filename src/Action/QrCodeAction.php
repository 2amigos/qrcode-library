<?php

/*
 * This file is part of the 2amigos/qrcode-library project.
 *
 * (c) 2amigOS! <http://2am.tech/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Da\QrCode\Action;

use Da\QrCode\Component\QrCodeComponent;
use Da\QrCode\Label;
use Yii;
use yii\base\Action;
use yii\web\Response;

class QrCodeAction extends Action
{
    /**
     * @var string the text to render if there are no parameter. Defaults to null, which means the component should
     *             render the text given as a parameter.
     */
    public $text;
    /**
     * @var string the parameter
     */
    public $param = 'text';
    /**
     * @var string whether the URL parameter is passed via GET or POST. Defaults to 'get'.
     */
    public $method = 'get';

    /**
     * @var string|Label|null
     */
    public $label = null;

    /**
     * @var array|null
     * 'r' => 0 //RED
     * 'g' => 0 //GREEN
     * 'B' => 0 //BLUE
     */
    public $background = null;

    /**
     * @var array|null
     * 'r' => 0 //RED
     * 'g' => 0 //GREEN
     * 'B' => 0 //BLUE
     * 'a  => 100 //ALPHA
     */
    public $foreground = null;
    /**
     * @var string the qr component name configured on the Yii2 app. The component should have configured all the
     *             possible options like adding a logo, styling, labelling, etc.
     */
    public $component = 'qr';

    /**
     * Runs the action.
     */
    public function run()
    {
        $text = call_user_func([Yii::$app->request, $this->method], $this->param, $this->text);
        $qrCode = Yii::$app->get($this->component);

        if ($text !== null && $qrCode instanceof QrCodeComponent) {
            Yii::$app->response->format = Response::FORMAT_RAW;
            Yii::$app->response->headers->add('Content-Type', $qrCode->getContentType());

            if ($this->label) {
                $qrCode->setLabel($this->label);
            }

            if (is_array($this->background)) {
                $qrCode->setBackgroundColor(
                    $this->background['r'],
                    $this->background['g'],
                    $this->background['b'],
                );
            }

            if (is_array($this->foreground)) {
                $qrCode->setForegroundColor(
                    $this->foreground['r'],
                    $this->foreground['g'],
                    $this->foreground['b'],
                    ! empty($this->foreground['a']) ? $this->foreground['a'] : 100,
                );
            }
            return $qrCode->setText((string)$text)->writeString();
        }
    }
}
