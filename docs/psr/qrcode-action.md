PSR-15 QrCode Action (Yii3, Mezzio, Slim, …)
============================================

`Da\QrCode\Bridge\Psr\QrCodeAction` is a framework-agnostic
[PSR-15](https://www.php-fig.org/psr/psr-15/) `RequestHandlerInterface`. It reads the QR text from a
request parameter (query string or parsed body), renders the QR code and returns a PSR-7 response
with the correct `Content-Type`. Because it depends only on PSR-7/PSR-17 interfaces, it works in any
PSR-15 application — **Yii3**, Mezzio, Slim, Laminas, and so on.

> The Yii2 integration lives separately at `Da\QrCode\Bridge\Yii2\QrCodeAction` (Yii2 does not use
> the PSR request/response cycle).

Requirements
------------

```bash
composer require psr/http-message psr/http-factory psr/http-server-handler
```

Your application already provides PSR-17 factories (`ResponseFactoryInterface` and
`StreamFactoryInterface`) — for example via [`nyholm/psr7`](https://github.com/Nyholm/psr7) or your
framework's container.

Configuration
-------------

The action is **immutable**: each `with*()` method returns a new, configured instance.

```php
use Da\QrCode\Bridge\Psr\QrCodeAction;

$action = (new QrCodeAction($responseFactory, $streamFactory))
    ->withParam('text')                 // request parameter to read (default: "text")
    ->withDefaultText('https://2am.tech')// used when the parameter is absent
    ->withSize(400)
    ->withMargin(12)
    ->withErrorCorrectionLevel(\Da\QrCode\Contracts\ErrorCorrectionLevelInterface::HIGH)
    ->withForegroundColor(20, 30, 90)
    ->withBackgroundColor(255, 255, 255)
    ->withLogo(__DIR__ . '/logo.png', 90)
    ->withLabel('2amigos')
    ->withWriter(new \Da\QrCode\Writer\SvgWriter()); // default writer is PNG (GD)
```

When the request has no text and no default is configured, the handler returns a `400` response.

Yii3
----

Register the action in your DI container and bind it to a route.

```php
// config/common/di.php
use Da\QrCode\Bridge\Psr\QrCodeAction;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;

return [
    QrCodeAction::class => static fn (ContainerInterface $c) => (new QrCodeAction(
        $c->get(ResponseFactoryInterface::class),
        $c->get(StreamFactoryInterface::class),
    ))->withSize(400),
];
```

```php
// config/common/routes.php
use Da\QrCode\Bridge\Psr\QrCodeAction;
use Yiisoft\Router\Route;

return [
    // The handler is resolved from the container. The array form is the most
    // portable across yiisoft/middleware-dispatcher versions; because the action
    // is also invokable, `->action(QrCodeAction::class)` works too.
    Route::get('/qr')->action([QrCodeAction::class, 'handle'])->name('qr'),
];
```

A request to `GET /qr?text=https://2am.tech` now returns the rendered QR image.

Any PSR-15 app (Mezzio / Slim)
------------------------------

The handler is a standard `RequestHandlerInterface`, so you can pipe or route to it directly. It is
also invokable (`$action($request)`), which suits routers that call handlers as callables.

```php
use Da\QrCode\Bridge\Psr\QrCodeAction;
use Nyholm\Psr7\Factory\Psr17Factory;

$psr17  = new Psr17Factory();
$action = new QrCodeAction($psr17, $psr17);

$response = $action->handle($request); // GET /qr?text=hello
// $response->getHeaderLine('Content-Type') === 'image/png'
```
