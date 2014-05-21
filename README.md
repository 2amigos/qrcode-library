QrCode Helper for Yii2
======================

QrCode helper allows you to render QrCodes on your Yii2 applications.

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require "2amigos/yii2-qrcode-helper" "*"
```
or add

```json
"2amigos/yii2-qrcode-helper" : "*"
```

to the require section of your application's `composer.json` file.

Usage
-----

The helper comes with some format helper classes that will help you to create the different type of QrCodes that a
mobile phone scanner will understand.

The library comes with the following formats:

- BookMark
- Geo
- MailMessage
- MailTo
- MeCard
- Phone
- Sms
- vCard
- Wifi

There are many more out there, we hope the community will helps us improve the library with `facebook`, `google maps`,
`youtube`, and `market` links. If not, we will add them whenever we have time :)

To render the qrcode, add this to your HTML page:

```html
<img src="<?= Url::to(['route/qrcode'])?>" />
```

Now, use it on your action:

```php
use doamigos\qrcode\formats\MailTo;
use dosamigos\qrcode\QrCode;

\\ ...

public function actionQrcode() {
    $mailTo = new MailTo(['email' => 'email@example.com']);
    return QrCode::png($mailTo->getText());
}

```

That's it, you should have a beautiful QrCode image on your website.


> [![2amigOS!](http://www.gravatar.com/avatar/55363394d72945ff7ed312556ec041e0.png)](http://www.2amigos.us)

<i>Web development has never been so fun!</i>
[www.2amigos.us](http://www.2amigos.us)