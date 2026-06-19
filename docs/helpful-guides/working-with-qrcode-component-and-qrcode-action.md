Working with QrCodeComponent and QrCodeAction
---------------------------------------------

First we need to configure the component in our Yii2 application config file on its `components` section: 

```php 

'components' => [
// ... 
    'qr' => [
        'class' => '\Da\QrCode\Bridge\Yii2\QrCodeComponent',
        'label' => '2am.tech',
        'size' => 500 // big and nice :D
        // ... you can configure more properties of the component here
    ]
// ...
]

```

Then simply add the action to your controller: 

```php

public function actions()
{
    return [
        'qr' => [
            'class' => \Da\QrCode\Bridge\Yii2\QrCodeAction::class,
            'text' => 'https://2am.tech', // default text
            'param' => 'v',
            'component' => 'qr' // if configured in our app as `qr` 
        ]
    ];
}
```

Now, this is one of our controller's action. We can call it as `http://example.com/<controller>/qr` and we will have our 
QrCode. According to the above configuration we could use it to display it on img tags on our views like this: 

```html
<img src="<?= Url::to(['controller/qr', 'v' => 'Hey! This is some content on my QrCode!']) ?>" />

<!-- this will display https://2am.tech (default text) -->
<img src="<?= Url::to(['controller/qr']) ?>" />
```
