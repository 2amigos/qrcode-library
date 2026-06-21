QrCode Library
================

[![Latest Version](https://img.shields.io/github/tag/2amigos/qrcode-library.svg?style=flat-square&label=release)](https://github.com/2amigos/qrcode-library/tags)
[![Software License](https://img.shields.io/badge/license-BSD-brightgreen.svg?style=flat-square)](LICENSE.md)
[![tests](https://github.com/2amigos/qrcode-library/actions/workflows/ci.yml/badge.svg)](https://github.com/2amigos/qrcode-library/actions/workflows/ci.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/2amigos/qrcode-library.svg)](https://packagist.org/packages/2amigos/qrcode-library) 

This library allows developers to generate QR codes with ease. It works standalone and also provides Yii2 components for 
its use on the framework.

## Requirements

- PHP 8.3, 8.4 or 8.5
- `ext-gd`
- `ext-simplexml`

PNG and JPG output is rendered with a pure-GD backend **by default**, so no ImageMagick installation is required. 
ImageMagick (`ext-imagick`) is **optional** and only needed if you explicitly opt into the Imagick backend — see the 
[upgrade guide](UPGRADE-4.0.md) and the [troubleshooting guide](docs/helpful-guides/troubleshooting.md).

## Documentation 

You can read the latest docs on [https://qrcode-library.readthedocs.io/en/latest/](https://qrcode-library.readthedocs.io/en/latest/)

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits

- [Zxing Wiki Barcode Contents](https://github.com/zxing/zxing/wiki/Barcode-Contents)
- [BaconQrCode](https://github.com/Bacon/BaconQrCode)
- [Endroid QrCode](https://github.com/endroid/QrCode)
- [Antonio Ramirez](https://github.com/tonydspaniard)
- [All Contributors](../../contributors)

## License

The BSD License (BSD). Please see [License File](LICENSE.md) for more information.


> [![2amigOS!](http://www.gravatar.com/avatar/55363394d72945ff7ed312556ec041e0.png)](https://2amigos.us)

<i>Web development has never been so fun!</i>  
[www.2am.tech](https://2am.tech)
