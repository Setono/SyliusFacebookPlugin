<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Functional\Http;

use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * The mock response factory of the test application's http client (see config/packages/test/framework.yaml).
 * It answers every request like the Conversions API does when it accepts an event, and records the request,
 * which also means that no test can ever send anything to Meta
 */
final class RecordingResponseFactory
{
    /** @var list<array{method: string, url: string, body: string}> */
    public array $requests = [];

    /**
     * @param array<string, mixed> $options
     */
    public function __invoke(string $method, string $url, array $options = []): ResponseInterface
    {
        $this->requests[] = ['method' => $method, 'url' => $url, 'body' => self::body($options['body'] ?? '')];

        return new MockResponse('{"events_received":1}', [
            'http_code' => 200,
            'response_headers' => ['Content-Type' => 'application/json'],
        ]);
    }

    private static function body(mixed $body): string
    {
        if (is_string($body)) {
            return $body;
        }

        if (!$body instanceof \Closure) {
            return '';
        }

        $content = '';
        while (is_string($chunk = $body(16372)) && '' !== $chunk) {
            $content .= $chunk;
        }

        return $content;
    }
}
