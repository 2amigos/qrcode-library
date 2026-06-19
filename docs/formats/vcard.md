VCardFormat
-----------

vCard is a file format standard for electronic business cards. vCards are often attached to e-mail messages, but can be
exchanged in other ways, such as on the World Wide Web or instant messaging. They can contain name and address 
information, telephone numbers, e-mail addresses, URLs, logos, photographs, and audio clips.

Usage
-----

```php 

use Da\QrCode\QrCode;
use Da\QrCode\Format\VCardFormat; 

$format = new VCardFormat();
$format->name = "Antonio";
$format->fullName = "Antonio Ramirez";
$format->email = "hola@2am.tech";

$qrCode = new QrCode($format);

header('Content-Type: ' . $qrCode->getContentType());

echo $qrCode->writeString();

```

Photo
-----

Since **4.0** (#69) the `photo` property accepts more than a remote URL. It is interpreted in the
following order of precedence:

- a ready `data:` URI (e.g. `data:image/png;base64,...`) — embedded inline as-is;
- a path to a readable local image file — read and embedded inline as a Base64 `data:` URI;
- a remote URL or path ending in a supported image extension (`jpeg`, `jpg`, `png`, `gif`) —
  referenced by URL, the historical behaviour.

```php
// Inline a local image file as Base64 (new in 4.0)
$format->photo = '/path/to/avatar.png';

// Inline a ready data URI as-is (new in 4.0)
$format->photo = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUg...';

// Reference a remote image by URL (back-compatible)
$format->photo = 'https://example.com/avatar.png';
```
