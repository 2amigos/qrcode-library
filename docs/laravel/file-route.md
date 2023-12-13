Laravel File Route
----

This library realeases a blade component to make it easy to build qrcode with the Laravel Framework.

Before get started, make sure you have the class `\Da\QrCode\Providers\QrCodeServiceProvider::class`
listed on you config/app.php file, on providers section.

```php
[
    ...
    'providers' => [
        ...
        \Da\QrCode\Providers\QrCodeServiceProvider::class,
    ],
];
```

The file route is registered as `/da-qrcode/build` and it has only one required parameter: `content`.

You can test this resource by accessing the endpoint, e.g `/da-qrcode/build?content=2am. Technologies` 

As optional parameters it has:

- label;
- margin; and
- size

Here is a complete sample of usage:

```html
<img src="/da-qrcode/build?content=2am.%20Technologies&margin=25&size=500&label=2am.%20Technologies"/>

<!-- or -->
<img src="{{route(
    'da-qrcode.build',
    [
        'content' => '2am ',
        'size' => 500,
        'margin' => 25,
        'label' => '2am. Technologies'
    ])}}"
/>
```