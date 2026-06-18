# CHANGELOG

## 4.0.0 - 2026-06-18

### Added
- Enh #69: vCard `PHOTO` now supports Base64 / `data:` URIs and local image files, inlined as a `PHOTO:data:<mime>;base64,...` line, in addition to the previous remote-URL behaviour (tonydspaniard)
- Enh #34: `MeCardFormat` gains an `organization` property, emitted as an `ORG:` entry only when set (tonydspaniard)
- Doc #32: Documented how to save / right-click-save the generated QR using `writeFile()` and `writeDataUri()` (tonydspaniard)
- New pure-GD render backend `Da\QrCode\Renderer\GdImageBackEnd` (tonydspaniard)
- New framework-agnostic PSR-15 `Da\QrCode\Bridge\Psr\QrCodeAction` (a `RequestHandlerInterface`) for Yii3 and any PSR-15 app (Mezzio, Slim, ...) (tonydspaniard)

### Changed
- Reorganized framework adapters under `Da\QrCode\Bridge\<Framework>\` (Yii2, Laravel, Psr); dropped redundant `Laravel` class-name prefixes. See [UPGRADE-4.0](UPGRADE-4.0.md) §7 for the namespace map (tonydspaniard)
- Enh #73: Require PHP 8.3, 8.4 or 8.5; dropped support for everything below 8.3 (tonydspaniard)
- Dropped the `marc-mabe/php-enum` dependency; `Enums\*` classes are now plain `final class`es exposing the same `public const` values (tonydspaniard)
- Bumped `bacon/bacon-qr-code` to `^3` and `khanamiryan/qrcode-detector-decoder` to `^2` (tonydspaniard)
- `PngWriter` and `JpgWriter` now render with the GD backend by default; ImageMagick is opt-in via the writer constructor (tonydspaniard)
- Renamed the misspelled `StyleManager::getGradientTye()` to `getGradientType()`, keeping a deprecated `getGradientTye()` alias until 3.0 (tonydspaniard)
- `QrCode::getGradientType()` now returns a `BaconQrCode\Renderer\RendererStyle\GradientType` instead of a `string` (tonydspaniard)
- `LaravelQrCodeFactory` now degrades gracefully to sensible defaults when Laravel's `config()` helper is not bound, so it can also be used standalone (tonydspaniard)

### Fixed
- Fix #68: PNG/JPG output no longer requires ImageMagick, avoiding the Windows `RegistryKeyLookupFailed 'CoderModulesPath'` error; only `ext-gd` is needed (tonydspaniard)
- Fixed radial and inverse-diagonal gradients rendering as solid black: the margin whitespace-trim looked for the exact foreground colour, which is absent at the edges of those gradients (tonydspaniard)

## 1.1.4 - 2026-06-18
- Fix #73: Add PHP 8.5 support with explicit nullable type hints (tonydspaniard)
- Fix #24: MeCard does not allow line breaks (Thoulah)

## 1.1.3 - Work in progress 

## 1.1.1-2 - August 25, 2017
- Enh: Ported PHPUnit tests to Codeception (tonydspaniard)
- Enh: Remove Yii2 dependencies for all classes but those made for Yii2 (tonydspaniard)

## 1.1.0 - August 24, 2017
- Enh #18: Initial new release. Total refactor (tonydspaniard)
