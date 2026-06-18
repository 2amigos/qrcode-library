<?php

namespace unit;

use Da\QrCode\Bridge\Psr\QrCodeAction;
use Da\QrCode\Writer\JpgWriter;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ServerRequestInterface;
use Zxing\QrReader;

class PsrQrCodeActionTest extends \Codeception\Test\Unit
{
    private Psr17Factory $factory;

    protected function _before()
    {
        $this->factory = new Psr17Factory();
    }

    private function request(array $query): ServerRequestInterface
    {
        return $this->factory->createServerRequest('GET', 'https://example.test/qr')->withQueryParams($query);
    }

    private function action(): QrCodeAction
    {
        return new QrCodeAction($this->factory, $this->factory);
    }

    public function testRendersScannablePngFromTextParam()
    {
        $response = $this->action()->handle($this->request(['text' => 'hello psr-15']));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('image/png', $response->getHeaderLine('Content-Type'));

        $blob = (string) $response->getBody();
        $this->assertSame('image/png', getimagesizefromstring($blob)['mime']);
        $this->assertSame('hello psr-15', (new QrReader($blob, QrReader::SOURCE_TYPE_BLOB))->text());
    }

    public function testInvokableFormBehavesLikeHandle()
    {
        $action = $this->action();
        $response = $action($this->request(['text' => 'invokable']));

        $this->assertSame(200, $response->getStatusCode());
        $blob = (string) $response->getBody();
        $this->assertSame('invokable', (new QrReader($blob, QrReader::SOURCE_TYPE_BLOB))->text());
    }

    public function testMissingTextReturns400()
    {
        $response = $this->action()->handle($this->request([]));

        $this->assertSame(400, $response->getStatusCode());
        $this->assertStringContainsString('Missing required parameter', (string) $response->getBody());
    }

    public function testDefaultTextIsUsedWhenParamAbsent()
    {
        $response = $this->action()->withDefaultText('fallback')->handle($this->request([]));

        $this->assertSame(200, $response->getStatusCode());
        $blob = (string) $response->getBody();
        $this->assertSame('fallback', (new QrReader($blob, QrReader::SOURCE_TYPE_BLOB))->text());
    }

    public function testCustomParamName()
    {
        $action = $this->action()->withParam('q');
        $response = $action->handle($this->request(['q' => 'custom-param']));

        $blob = (string) $response->getBody();
        $this->assertSame('custom-param', (new QrReader($blob, QrReader::SOURCE_TYPE_BLOB))->text());
    }

    public function testWriterAndOptionsAreApplied()
    {
        $response = $this->action()
            ->withWriter(new JpgWriter())
            ->withSize(400)
            ->withForegroundColor(20, 30, 90)
            ->handle($this->request(['text' => 'options']));

        $this->assertSame('image/jpeg', $response->getHeaderLine('Content-Type'));
        $info = getimagesizefromstring((string) $response->getBody());
        $this->assertSame('image/jpeg', $info['mime']);
    }

    public function testConfigurationIsImmutable()
    {
        $base = $this->action();
        $modified = $base->withParam('other');

        $this->assertNotSame($base, $modified);

        // The original still reads the default 'text' parameter.
        $response = $base->handle($this->request(['text' => 'still-default']));
        $this->assertSame(200, $response->getStatusCode());
    }
}
