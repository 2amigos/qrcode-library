<?php

namespace Da\QrCode\Action;

use Da\QrCode\Component\QrCodeComponent;
use Da\QrCode\Contracts\ErrorCorrectionLevelInterface;
use Da\QrCode\Contracts\LabelInterface;
use Da\QrCode\Label;
use Yii;
use yii\base\Action;

class QrCodeAction extends Action
{
    /**
     * @var string the text to render if there are no parameter. Defaults to null, which means the component should
     * render the text given as a parameter.
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
     * @var string the qr component name configured on the Yii2 app.
     */
    public $component = 'qr';
    /**
     * @var string the logo to be included in the qr code. If applied, errorCorrectionLevel will be updated to HIGH.
     */
    public $logo;
    /**
     * @var string the label to add to the qr code. Defaults to null, which means no label.
     */
    public $label;
    /**
     * @var int the label font size. Defaults to 14.
     */
    public $labelFontSize = 14;
    /**
     * @var string the label alignment. Defaults to center.
     */
    public $labelAlignment = LabelInterface::ALIGN_CENTER;

    /**
     * Runs the action.
     */
    public function run()
    {
        $text = call_user_func_array([Yii::$app->request, $this->method], [$this->param, $this->text]);

        $qr = Yii::$app->get($this->component);

        if ($text && $qr instanceof QrCodeComponent) {
            $qr->setText($text);

            if ($this->logo) {
                $qr
                    ->useLogo($this->logo)
                    ->setErrorCorrectionLevel(ErrorCorrectionLevelInterface::HIGH);
            }
            if ($this->label) {
                $label = new Label($this->label, null, $this->labelFontSize, $this->labelAlignment);

                $qr->setLabel($label);
            }

            header('Content-Type: ' . $qr->getContentType());
            echo $qr->writeString();
        }
    }
}
