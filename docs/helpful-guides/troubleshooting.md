Troubleshooting
===============

This guide collects common issues and how to resolve them.

ImageMagick `RegistryKeyLookupFailed 'CoderModulesPath'` error (#68)
-------------------------------------------------------------------

### Symptom

When generating a PNG or JPG QR code (typically on Windows / XAMPP) you see an error similar to:

```
ImagickException: RegistryKeyLookupFailed `CoderModulesPath' @ error/module.c/GetMagickModulePath
```

### Why it happened

In 3.x, `PngWriter` and `JpgWriter` **forced** the ImageMagick (Imagick) backend. That required the
`ext-imagick` extension to be installed *and correctly configured*. When ImageMagick could not
locate its coder modules (a common misconfiguration on Windows), Imagick threw the
`RegistryKeyLookupFailed 'CoderModulesPath'` error and no image could be produced.

### The fix in 2.0

As of 2.0 the writers render with a **pure-GD backend by default**
([`Da\QrCode\Renderer\GdImageBackEnd`](../../src/Renderer/GdImageBackEnd.php)), so PNG and JPG output
only needs `ext-gd`, which the library already requires. **The error no longer occurs out of the
box** — you do not need ImageMagick at all.

```php
use Da\QrCode\QrCode;

// Uses the default GD backend — no ImageMagick required.
$qrCode = new QrCode('https://2am.tech');
$qrCode->writeFile(__DIR__ . '/code.png');
```

**Recommendation:** simply use the default GD backend. It works everywhere `ext-gd` is enabled and
avoids the ImageMagick configuration headache entirely.

### If you explicitly opt into ImageMagick

ImageMagick is still supported as an opt-in backend. Pass it to the writer constructor (see
[`PngWriter`](../../src/Writer/PngWriter.php) / [`JpgWriter`](../../src/Writer/JpgWriter.php)):

```php
use Da\QrCode\QrCode;
use Da\QrCode\Writer\PngWriter;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;

$writer  = new PngWriter(new ImagickImageBackEnd('png'));
$qrCode  = new QrCode('https://2am.tech', null, $writer);
$qrCode->writeFile(__DIR__ . '/code.png');
```

If you go this route, make sure ImageMagick is installed and configured correctly on Windows
(XAMPP):

1. **Install the ImageMagick binaries** matching your PHP build. Pick the Windows installer whose
   architecture (x64/x86) and "thread safety" (TS/NTS) match your PHP. Install it to a path without
   spaces, e.g. `C:\ImageMagick`.

2. **Add ImageMagick to the system `PATH`** and set the `MAGICK_HOME` (and, on some builds,
   `MAGICK_CODER_MODULE_PATH`) environment variable so Imagick can find its **coder modules** — this
   is exactly what the `CoderModulesPath` lookup needs:

   ```
   MAGICK_HOME=C:\ImageMagick
   PATH=...;C:\ImageMagick
   ```

   Restart Apache (XAMPP) after changing environment variables so the new `PATH` is picked up.

3. **Enable the Imagick PHP extension.** Drop `php_imagick.dll` into your PHP `ext\` directory and
   add the following to `php.ini` (the one XAMPP actually loads — check `phpinfo()`):

   ```ini
   extension=imagick
   ```

   Restart Apache and verify with `phpinfo()` that the `imagick` section appears and lists the
   coder/format support you need (PNG, JPEG).

If the `CoderModulesPath` error persists, it almost always means the ImageMagick install path or the
`MAGICK_HOME`/`PATH` environment is wrong. The simplest, most reliable option remains the **default
GD backend** — no ImageMagick required.


Saving / "right-click → save" the generated QR code (#32)
---------------------------------------------------------

There are two common ways to make a generated QR code available to a user.

### Save the image to disk

Use `writeFile()` with the destination path. The writer is inferred from the file extension when you
do not pass one explicitly (PNG by default):

```php
use Da\QrCode\QrCode;

$qrCode = new QrCode('https://2am.tech');

// Writes the PNG bytes to the given path on the server.
$qrCode->writeFile('/path/to/qr.png');
```

### Embed it so the user can right-click and save it

Use `writeDataUri()` to get a `data:` URI you can drop straight into an `<img>` tag. The browser
renders it inline, and the user can right-click the image and choose *Save image as…*:

```php
use Da\QrCode\QrCode;

$qrCode = new QrCode('https://2am.tech');

echo '<img src="' . $qrCode->writeDataUri() . '" alt="QR code">';
```

Both approaches use the default GD backend, so neither requires ImageMagick.


© [2amigos](https://2am.tech/)
