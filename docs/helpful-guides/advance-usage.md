Advance Usage 
-------------

When setting multiple options to the QrCode instance, we have to remember that this class provides immutability. That is,
every time we change an attribute it returns a cloned copy of the instance.

> Immutability: The true constant is change. Mutation hides change. Hidden change manifests chaos. Therefore, the wise 
> embrace history. 
> Source - [The Dao of Immutability](https://medium.com/javascript-scene/the-dao-of-immutability-9f91a70c88cd)

Remembering that fact, we can configure and use our instance like this:


```php 

// A label can be a string OR a Da\Contracts\LabelInterface instance. 
// Using the instance, we will have more control on how do we want the label to be displayed.
// Immutability also applies to this class! 
$label = (new Label('2amigos'))
    ->setFont(__DIR__ . '/../resources/fonts/monsterrat.otf')
    ->setFontSize(12);

$qrCode = (new QrCode('https://2am.tech'))
    ->setLogo(__DIR__ . '/data/logo.png')
    ->setForegroundColor(51, 153, 255)
    ->setBackgroundColor(200, 220, 210)
    ->setEncoding('UTF-8')
    ->setErrorCorrectionLevel(ErrorCorrectionLevelInterface::HIGH)
    ->setLogoWidth(48) // recommended to be 16% of qrcode width
    ->setSize(300)
    ->setMargin(5)
    ->setLabel($label);
    
$qrCode->writeFile(__DIR__ . '/codes/my-code.png');

```

By default, the logo image height will be transformed to fit the image width,
keeping a square shaped pattern. You can choose to scale the height 
instead:

```PHP
$qrCode = (new QrCode('https://2am.tech'))
    ->setLogo(__DIR__ . '/data/logo.png')
    ->setLogoWidth(48)
    ->setScaleLogoHeight(false);
```

### Gradient Foreground

You can easily change from uniform to gradient color by setting 
the range limit for you gradient for using the method `setForegroundEndColor`.
When you do this, the color you've set for the foreground using `setForegroundColor`
function will be taken as the start point for the gradient color.

```PHP
$qrCode = (new QrCode('https://2am.tech'))
    ->setForegroundColor(0, 255, 0, 70)
    ->setForegroundEndColor(0, 0, 255, 50);
```

By default, it will render the gradient color using a vertical orientation.
You can change it by the `setGradientType` function. It takes one parameter 
to determine witch gradient type must be set.
The available types are:

* \Da\QrCode\Contracts\ColorsInterface::GRADIENT_VERTICAL
* \Da\QrCode\Contracts\ColorsInterface::GRADIENT_HORIZONTAL
* \Da\QrCode\Contracts\ColorsInterface::GRADIENT_RADIAL
* \Da\QrCode\Contracts\ColorsInterface::GRADIENT_DIAGONAL
* \Da\QrCode\Contracts\ColorsInterface::GRADIENT_INVERSE_DIAGONAL

```PHP
$qrCode = (new QrCode('https://2am.tech'))
    ->setForegroundColor(0, 255, 0, 70)
    ->setForegroundEndColor(0, 0, 255, 50)
    ->setGradientType(\Da\QrCode\Contracts\ColorsInterface::GRADIENT_DIAGONAL);
```

### Foreground Path Style
It is possible to change the foreground path style by using the `setPathStyle`
function. The default style will be the square pattern, and the available
styles are the following:

* \Da\QrCode\Contracts\PathStyleInterface::SQUARE;
* \Da\QrCode\Contracts\PathStyleInterface::DOTS;
* \Da\QrCode\Contracts\PathStyleInterface::ROUNDED; 

```PHP
$qrCode = (new QrCode('https://2am.tech'))
    ->setPathStyle(\Da\QrCode\Contracts\PathStyleInterface::ROUNDED);
```

You can also set the intensity for the pattern appliance:

```PHP
$qrCode = (new QrCode('https://2am.tech'))
    ->setPathStyle(\Da\QrCode\Contracts\PathStyleInterface::ROUNDED)
    ->setPathIntensity(0.7);
```

The default value for the intensity is 1. It must be a number between 0 and 1,
otherwise an exception will be thrown.

© [2amigos](https://2am.tech/) 2013-2023
