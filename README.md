QrCode Helper for Yii2
======================

[![Latest Version](https://img.shields.io/github/tag/2amigos/yii2-qrcode-helper.svg?style=flat-square&label=release)](https://github.com/2amigos/yii2-qrcode-helper/tags)
[![Software License](https://img.shields.io/badge/license-BSD-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/2amigos/yii2-qrcode-helper/master.svg?style=flat-square)](https://travis-ci.org/2amigos/yii2-qrcode-helper)
[![Coverage Status](https://img.shields.io/scrutinizer/coverage/g/2amigos/yii2-qrcode-helper.svg?style=flat-square)](https://scrutinizer-ci.com/g/2amigos/yii2-qrcode-helper/code-structure)
[![Quality Score](https://img.shields.io/scrutinizer/g/2amigos/yii2-qrcode-helper.svg?style=flat-square)](https://scrutinizer-ci.com/g/2amigos/yii2-qrcode-helper)
[![Total Downloads](https://img.shields.io/packagist/dt/2amigos/yii2-qrcode-helper.svg?style=flat-square)](https://packagist.org/packages/2amigos/yii2-qrcode-helper)


QrCode helper allows you to render QrCodes on your Yii2 applications.

Installation
------------
The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require 2amigos/yii2-qrcode-helper:~1.0
```
or add

```json
"2amigos/yii2-qrcode-helper" : "~1.0"
```

to the require section of your application's `composer.json` file.

Usage
-----

The helper comes with some format helper classes that will help you to create the different type of QrCodes that a
mobile phone scanner will understand.

The library comes with the following formats:

- Bitcoin
- BookMark
- Geo
- iCal
- MailMessage
- MailTo
- MeCard
- MMS
- Phone
- Sms
- vCard
- Wifi
- Youtube

There are many more out there, we hope the community will helps us improve the library with `facebook`, `google maps`,
`youtube`, and `market` links. If not, we will add them whenever we have time :)

To render the qrcode, add this to your HTML page:

```html
<img src="<?= Url::to(['route/qrcode'])?>" />
```

Now, use it on your action:

```php
use dosamigos\qrcode\formats\MailTo;
use dosamigos\qrcode\QrCode;

\\ ...

public function actionQrcode() {
    $mailTo = new MailTo(['email' => 'email@example.com']);
    return QrCode::png($mailTo->getText());
    // you could also use the following
    // return return QrCode::png($mailTo);
}

```

That's it, you should have a beautiful QrCode image on your website.

## Testing

``` bash
$ phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Antonio Ramirez](https://github.com/tonydspaniard)
- [All Contributors](../../contributors)

## License

The BSD License (BSD). Please see [License File](LICENSE.md) for more information.


> [![2amigOS!](http://www.gravatar.com/avatar/55363394d72945ff7ed312556ec041e0.png)](http://www.2amigos.us)

<i>Web development has never been so fun!</i>  
[www.2amigos.us](http://www.2amigos.us)
