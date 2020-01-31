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

$qrCode = (new QrCode('https://2amigos.us'))
    ->setLogo(__DIR__ . '/data/logo.png')
    ->setForegroundColor(51, 153, 255)
    ->setBackgroundColor(200, 220, 210)
    ->setEncoding('UTF-8')
    ->setErrorCorrectionLevel(ErrorCorrectionLevelInterface::HIGH)
    ->setLogoWidth(60)
    ->setSize(300)
    ->setMargin(5)
    ->setLabel($label);
    
$qrCode->writeFile(__DIR__ . '/codes/my-code.png');

```


© [2amigos](https://2amigos.us/) 2013-2020
