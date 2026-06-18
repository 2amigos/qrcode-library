# Upgrading from 3.x to 4.0

This guide lists every breaking change in **4.0** and what to do about each one. For most
applications the upgrade is straightforward: the public API of the formats, the `QrCode`
class and the `Da\QrCode\Enums\*` constants is unchanged.

## Quick checklist

- [ ] Run on PHP 8.3, 8.4 or 8.5.
- [ ] Stop relying on the ImageMagick extension for PNG/JPG output (GD is now the default).
- [ ] If you call MabeEnum instance/static methods on the `Enums\*` classes, replace them with the plain constants.
- [ ] Re-run any image snapshot/byte-comparison tests for PNG/JPG output.
- [ ] Replace `StyleManager::getGradientTye()` with `StyleManager::getGradientType()`.
- [ ] Treat `QrCode::getGradientType()` as returning a `GradientType` object, not a `string`.
- [ ] Update framework-adapter namespaces — they moved under `Da\QrCode\Bridge\<Framework>\` (see §7).

---

## 1. Minimum PHP version is now 8.3

Everything below PHP 8.3 has been dropped. `composer.json` now requires `"php": "^8.3"`,
so the supported versions are **8.3, 8.4 and 8.5**.

**What to do:** Upgrade your runtime to PHP 8.3 or newer before installing 4.0. If you must stay
on an older PHP, remain on the 3.x line.

## 2. `marc-mabe/php-enum` dependency removed

The `marc-mabe/php-enum` package is no longer a dependency. The classes under
[src/Enums/](src/Enums/) — [`Format`](src/Enums/Format.php), [`Gradient`](src/Enums/Gradient.php),
[`Label`](src/Enums/Label.php) and [`Path`](src/Enums/Path.php) — are now plain `final class`es that
expose the **same** `public const` values as before.

This means the constants you already use keep working unchanged:

```php
use Da\QrCode\Enums\Format;
use Da\QrCode\Enums\Path;

Format::TEXT;   // 'text'
Path::DOTS;     // unchanged
```

**What to do:** Nothing, unless you called MabeEnum *instance or static methods* on these classes
(for example `Format::TEXT()`, `->getValue()`, `::getValues()`, `::byValue()`). Those methods no
longer exist; use the plain constants instead. This usage was rare.

## 3. Default render backend is now GD (ImageMagick is opt-in)

Previously, `PngWriter` and `JpgWriter` forced the ImageMagick backend, which required the
`ext-imagick` extension and, on some Windows setups, produced the error
`RegistryKeyLookupFailed 'CoderModulesPath' ... GetMagickModulePath` (issue #68).

As of 4.0, both writers render with a new pure-GD backend
([`Da\QrCode\Renderer\GdImageBackEnd`](src/Renderer/GdImageBackEnd.php)) **by default**, so only
`ext-gd` is required (the library already required it).

Because the rendering engine changed, the **raw bytes of generated PNG/JPG images differ slightly**
from 3.x, even when the QR content is identical.

ImageMagick is still supported as an **opt-in** backend — pass it to the writer constructor:

```php
use Da\QrCode\QrCode;
use Da\QrCode\Writer\PngWriter;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;

// Opt into the ImageMagick backend (requires ext-imagick)
$writer = new PngWriter(new ImagickImageBackEnd('png'));

$qrCode = new QrCode('https://2am.tech', null, $writer);
$qrCode->writeFile(__DIR__ . '/code.png');
```

The same pattern applies to [`JpgWriter`](src/Writer/JpgWriter.php) using
`new ImagickImageBackEnd('jpeg')`.

**What to do:**

- If you relied on ImageMagick output, either remove that assumption (recommended — GD now works out
  of the box) or explicitly opt in with the constructor argument shown above.
- If you compare generated PNG/JPG bytes in tests (snapshot/golden tests), regenerate your fixtures.
- See [docs/helpful-guides/troubleshooting.md](docs/helpful-guides/troubleshooting.md) for the
  ImageMagick `RegistryKeyLookupFailed` error and Windows setup details.

## 4. `bacon/bacon-qr-code` bumped to `^3` (and the decoder to `^2`)

The underlying [`bacon/bacon-qr-code`](https://github.com/Bacon/BaconQrCode) dependency was upgraded
to `^3.0`, and `khanamiryan/qrcode-detector-decoder` to `^2.0`.

**What to do:** If you reference BaconQrCode classes directly (renderers, backends, renderer-style
objects), review their v3 API for any signature changes. If you only use this library's own API, no
action is needed.

## 5. `StyleManager::getGradientTye()` renamed to `getGradientType()`

The misspelled method `StyleManager::getGradientTye()` has been corrected to
[`getGradientType()`](src/StyleManager.php). A **deprecated** `getGradientTye()` alias remains for
backward compatibility and will be **removed in 3.0**.

**What to do:** Replace any calls to `getGradientTye()` with `getGradientType()`. The alias still
works for now but will emit deprecations and disappear in 3.0.

## 6. `QrCode::getGradientType()` return type changed

[`QrCode::getGradientType()`](src/QrCode.php) (and `StyleManager::getGradientType()`) now returns a
`BaconQrCode\Renderer\RendererStyle\GradientType` instance. It was previously declared as `string`.

**What to do:** If you consumed the return value as a string, update your code to work with the
`GradientType` object instead. If you only pass it back into the library, no action is needed.

## 7. Framework adapters moved under `Da\QrCode\Bridge\<Framework>\`

The framework-specific classes were scattered across `Action/`, `Component/`, `Controllers/`,
`Providers/` and `Factory/`. They are now grouped by framework under `Da\QrCode\Bridge\`, and the
redundant `Laravel` prefixes were dropped. The framework-agnostic core (`QrCode`, `StyleManager`,
`Writer\*`, `Format\*`, `Renderer\*`, `Factory\WriterFactory`, the `Enums`, etc.) is **unchanged**.

| 3.x class | 4.0 class |
| --- | --- |
| `Da\QrCode\Action\QrCodeAction` | `Da\QrCode\Bridge\Yii2\QrCodeAction` |
| `Da\QrCode\Component\QrCodeComponent` | `Da\QrCode\Bridge\Yii2\QrCodeComponent` |
| `Da\QrCode\Component\QrCodeBladeComponent` | `Da\QrCode\Bridge\Laravel\QrCodeBladeComponent` |
| `Da\QrCode\Controllers\LaravelResourceController` | `Da\QrCode\Bridge\Laravel\ResourceController` |
| `Da\QrCode\Providers\QrCodeServiceProvider` | `Da\QrCode\Bridge\Laravel\QrCodeServiceProvider` |
| `Da\QrCode\Factory\LaravelQrCodeFactory` | `Da\QrCode\Bridge\Laravel\QrCodeFactory` |

**What to do:**

- **Yii2:** update the `class` of your `qr` component and the `use` for the action to the new
  `Da\QrCode\Bridge\Yii2\…` namespaces.
- **Laravel:** the service provider is auto-discovered, so most apps need no change. If you registered
  it manually in `config/app.php`, update it to `Da\QrCode\Bridge\Laravel\QrCodeServiceProvider`. If
  you referenced `LaravelQrCodeFactory` directly, it is now `Bridge\Laravel\QrCodeFactory`.

## 8. New: framework-agnostic PSR-15 action (Yii3, Mezzio, Slim, …)

`4.0` adds [`Da\QrCode\Bridge\Psr\QrCodeAction`](src/Bridge/Psr/QrCodeAction.php), a PSR-15
`RequestHandlerInterface` that renders a QR from a request parameter and depends only on PSR-7/PSR-17
interfaces. Use it with **Yii3** or any PSR-15 application. It requires `psr/http-message`,
`psr/http-factory` and `psr/http-server-handler` (all listed under `suggest`).

```php
use Da\QrCode\Bridge\Psr\QrCodeAction;

$action = (new QrCodeAction($responseFactory, $streamFactory))
    ->withSize(400)
    ->withForegroundColor(20, 30, 90);

$response = $action->handle($request); // e.g. GET /qr?text=hello
```

See [docs/psr/qrcode-action.md](docs/psr/qrcode-action.md) for a full Yii3 wiring example.

---

© [2amigos](https://2am.tech/)
