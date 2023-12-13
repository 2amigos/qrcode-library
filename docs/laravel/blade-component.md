Laravel Blade Component
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

With the provider set, we can create a qrcode using the `2am-qrcode` blade component.
It has only `content` as a required field.

```html
<x-2am-qrcode :content="'2am. Technologies'"/>
```

We can also define the qrcode [format](../index.md#Formats). To do so,
you must specify the `format` attribute with a constant from `\Da\QrCode\Enum\Format` and the `content` as an array,
fulfilling the data for the designed format as specified in the format [docs]((../index.md#Formats)).

To work with colors (background, foreground and gradient foreground), you set
the attributes `background`, `foregroud` and `foreground2` (for gradient foreground) as an array
having the keys `r`, `g`, `b` (and `a` to set alpha on foreground, but it's optional).

```php
$background = [
    'r' => 200,
    'g' => 200,
    'b' => 200,
];

$foreground = [
    'r' => 0,
    'b' => 255,
    'g' => 0,
];

$foreground2 = [
    'r' => 0,
    'b' => 0,
    'g' => 255,
];
      
$content = [
    'title' => '2am. Technologies',
    'url' => 'https://2am.tech',
];
```

```html
<x-2am-qrcode
    :content="$content"
    :format="\Da\QrCode\Enums\Format::BOOK_MARK"
    :background="$background"
    :foreground="$foreground"
    :foreground2="$foreground2"
/>
```

All blade component attributes:

| Attribute |                                Description                                 |                   Data Type                    |
|:---------:|:--------------------------------------------------------------------------:|:----------------------------------------------:|
|  content  |                         Defines the qrcode's data                          |                 string; array                  |
| format |                        Defines the qrcode's format                         |             \Da\QrCode\Enum\Format             |
| foreground |                 Defines the qrcode`s foreground base color                 |               array (r, g, b, a)               |
| background |                   Defines the qrcode's background color                    |               array (r, g, b, a)               |
| foreground2 |       Defines the qrcode's foreground end color (turns to gradient)        |               array (r, g, b, a)               |
| pathStyle |                      Defines the qrcode's path style                       |              \Da\QrCode\Enum\Path              |
| intensity |                      Defines the path style intensity                      |              float, from 0.1 to 1              |
| margin |                        Defines the qrcode's margin                         |                      int                       |
| size |                         Defines the qrcode's size                          |                      int                       |
| logoPath |             Set a image to be displayed in the qrcode's center             |         string; it should be full path         |
| logoSize |                        Set the qrcode's logo size.                         | int. Recomended size is 16% from qrcode's size |
| scaleLogoHeight | Set if the logo's image should be scaled instead of croped to square shape |             bool. Default is false             |
| gradientType |                         Defines the gradient type                          |           \Da\QrCode\Enums\Gradient            | 
| label |                         Defines the qrcode's label                         | string |
| font |                           Defines the label font                           | string. It should be full path |
| fontSize |                        Defines the label font size                         | int |
| fontAlign | Defines the label alignment | \Da\QrCode\Label |