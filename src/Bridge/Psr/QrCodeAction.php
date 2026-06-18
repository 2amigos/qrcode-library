<?php

/*
 * This file is part of the 2amigos/qrcode-library project.
 *
 * (c) 2amigOS! <http://2am.tech/>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Da\QrCode\Bridge\Psr;

use Da\QrCode\Contracts\LabelInterface;
use Da\QrCode\Contracts\WriterInterface;
use Da\QrCode\QrCode;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Framework-agnostic PSR-15 request handler that renders a QR code from a request parameter.
 *
 * It depends only on PSR-7/PSR-17 interfaces, so it works with any PSR-15 application — Yii3,
 * Mezzio, Slim, Laminas, etc. The Yii2 integration ({@see \Da\QrCode\Bridge\Yii2\QrCodeAction}) is kept
 * separately for the Yii2 (non-PSR) request/response cycle.
 *
 * Configuration is immutable: every `with*()` method returns a new, modified instance.
 *
 * Example (any PSR-15 router):
 * ```php
 * $action = (new QrCodeAction($responseFactory, $streamFactory))
 *     ->withSize(400)
 *     ->withForegroundColor(20, 30, 90);
 * $response = $action->handle($request); // GET /qr?text=hello
 * ```
 */
final class QrCodeAction implements RequestHandlerInterface
{
    private ResponseFactoryInterface $responseFactory;

    private StreamFactoryInterface $streamFactory;

    /** The request parameter (query or parsed body) that holds the QR text. */
    private string $param = 'text';

    /** Fallback text used when the request parameter is absent. */
    private ?string $defaultText = null;

    private ?WriterInterface $writer = null;

    private ?int $size = null;

    private ?int $margin = null;

    private ?string $encoding = null;

    private ?string $errorCorrectionLevel = null;

    /** @var array{r: int, g: int, b: int, a?: int}|null */
    private ?array $foreground = null;

    /** @var array{r: int, g: int, b: int}|null */
    private ?array $background = null;

    private LabelInterface|string|null $label = null;

    private ?string $logoPath = null;

    private ?int $logoWidth = null;

    public function __construct(ResponseFactoryInterface $responseFactory, StreamFactoryInterface $streamFactory)
    {
        $this->responseFactory = $responseFactory;
        $this->streamFactory = $streamFactory;
    }

    /**
     * Builds and returns the QR code image response, or a 400 response when no text is available.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $text = $this->resolveText($request);

        if ($text === null || $text === '') {
            return $this->responseFactory
                ->createResponse(400)
                ->withHeader('Content-Type', 'text/plain; charset=utf-8')
                ->withBody($this->streamFactory->createStream(
                    sprintf('Missing required parameter: "%s".', $this->param)
                ));
        }

        $qrCode = $this->buildQrCode($text);
        $body = $this->streamFactory->createStream($qrCode->writeString());

        return $this->responseFactory
            ->createResponse(200)
            ->withHeader('Content-Type', $qrCode->getContentType())
            ->withBody($body);
    }

    /**
     * Convenience invokable form for routers that call handlers directly.
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        return $this->handle($request);
    }

    public function withParam(string $param): self
    {
        return $this->with('param', $param);
    }

    public function withDefaultText(?string $text): self
    {
        return $this->with('defaultText', $text);
    }

    public function withWriter(WriterInterface $writer): self
    {
        return $this->with('writer', $writer);
    }

    public function withSize(int $size): self
    {
        return $this->with('size', $size);
    }

    public function withMargin(int $margin): self
    {
        return $this->with('margin', $margin);
    }

    public function withEncoding(string $encoding): self
    {
        return $this->with('encoding', $encoding);
    }

    public function withErrorCorrectionLevel(string $errorCorrectionLevel): self
    {
        return $this->with('errorCorrectionLevel', $errorCorrectionLevel);
    }

    public function withForegroundColor(int $red, int $green, int $blue, int $alpha = 100): self
    {
        return $this->with('foreground', ['r' => $red, 'g' => $green, 'b' => $blue, 'a' => $alpha]);
    }

    public function withBackgroundColor(int $red, int $green, int $blue): self
    {
        return $this->with('background', ['r' => $red, 'g' => $green, 'b' => $blue]);
    }

    public function withLabel(LabelInterface|string $label): self
    {
        return $this->with('label', $label);
    }

    public function withLogo(string $logoPath, ?int $logoWidth = null): self
    {
        $clone = $this->with('logoPath', $logoPath);
        $clone->logoWidth = $logoWidth;

        return $clone;
    }

    private function resolveText(ServerRequestInterface $request): ?string
    {
        $value = $request->getQueryParams()[$this->param] ?? null;

        if ($value === null) {
            $body = $request->getParsedBody();
            $value = is_array($body) ? ($body[$this->param] ?? null) : null;
        }

        if ($value === null) {
            return $this->defaultText;
        }

        return is_scalar($value) ? (string) $value : null;
    }

    private function buildQrCode(string $text): QrCode
    {
        $qrCode = new QrCode($text, $this->errorCorrectionLevel, $this->writer);

        if ($this->size !== null) {
            $qrCode->setSize($this->size);
        }
        if ($this->margin !== null) {
            $qrCode->setMargin($this->margin);
        }
        if ($this->encoding !== null) {
            $qrCode->setEncoding($this->encoding);
        }
        if ($this->foreground !== null) {
            $qrCode->setForegroundColor(
                $this->foreground['r'],
                $this->foreground['g'],
                $this->foreground['b'],
                $this->foreground['a'] ?? 100
            );
        }
        if ($this->background !== null) {
            $qrCode->setBackgroundColor($this->background['r'], $this->background['g'], $this->background['b']);
        }
        if ($this->logoPath !== null) {
            $qrCode->setLogo($this->logoPath);

            if ($this->logoWidth !== null) {
                $qrCode->setLogoWidth($this->logoWidth);
            }
        }
        if ($this->label !== null) {
            $qrCode->setLabel($this->label);
        }

        return $qrCode;
    }

    /**
     * @param mixed $value
     */
    private function with(string $property, $value): self
    {
        $clone = clone $this;
        $clone->$property = $value;

        return $clone;
    }
}
