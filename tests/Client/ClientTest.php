<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusFacebookPlugin\Client;

use PHPUnit\Framework\TestCase;
use Setono\SyliusFacebookPlugin\Client\Client;
use Setono\SyliusFacebookPlugin\Model\Pixel;
use Setono\SyliusFacebookPlugin\Model\PixelEvent;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Webmozart\Assert\Assert;

/**
 * @covers \Setono\SyliusFacebookPlugin\Client\Client
 */
final class ClientTest extends TestCase
{
    private bool $live = false;

    private string $accessToken = 'ACCESS_TOKEN';

    private string $pixelId = 'PIXEL_ID';

    private ?string $testEventCode = null;

    protected function setUp(): void
    {
        $live = (bool) getenv('FACEBOOK_LIVE');
        if (false !== $live) {
            $this->live = true;

            $accessToken = getenv('FACEBOOK_ACCESS_TOKEN');
            if (false !== $accessToken && '' !== $accessToken) {
                $this->accessToken = $accessToken;
            }

            $pixelId = getenv('FACEBOOK_PIXEL_ID');
            if (false !== $pixelId && '' !== $pixelId) {
                $this->pixelId = $pixelId;
            }

            $testEventCode = getenv('FACEBOOK_TEST_EVENT_CODE');
            if (false !== $testEventCode && '' !== $testEventCode) {
                $this->testEventCode = $testEventCode;
            }
        }
    }

    /**
     * @test
     */
    public function send_pixel_event_test(): void
    {
        $pixel = new Pixel();
        $pixel->setPixelId($this->pixelId);

        $eventTime = time();

        $pixelEvent = new PixelEvent();
        $pixelEvent->setPixel($pixel);
        $pixelEvent->setData([
            'user_data' => [
                'client_ip_address' => '::1',
                'client_user_agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/94.0.4606.61 Safari/537.36',
            ],
            'event_name' => 'ViewContent',
            'event_time' => $eventTime,
            'custom_data' => [
                'contents' => [
                    [
                        'id' => 'Beige_strappy_summer_dress',
                        'quantity' => 1,
                    ],
                ],
                'content_ids' => [
                    'Beige_strappy_summer_dress',
                ],
                'content_name' => 'Beige strappy summer dress',
                'content_type' => 'product',
              ],
              'action_source' => 'website',
              'event_source_url' => 'https://localhost:8000/en_US/products/beige-strappy-summer-dress',
        ]);

        $response = new MockResponse('{"events_received": 1}');
        $httpClient = $this->getHttpClient($response);
        $client = new Client($httpClient, 'v13.0', $this->accessToken, $this->testEventCode);
        $eventsReceived = $client->sendPixelEvent($pixelEvent);

        if ($this->live) {
            self::assertSame(1, $eventsReceived);
        } else {
            self::assertSame('POST', $response->getRequestMethod());
            self::assertSame(sprintf('https://graph.facebook.com/v13.0/%s/events', $this->pixelId), $response->getRequestUrl());

            $requestOptions = $response->getRequestOptions();

            Assert::keyExists($requestOptions, 'headers');
            $requestHeaders = $requestOptions['headers'];
            Assert::isArray($requestHeaders);
            self::assertContains('Content-Type: application/x-www-form-urlencoded', $requestHeaders);

            Assert::keyExists($requestOptions, 'body');
            Assert::string($requestOptions['body']);
            $requestBody = urldecode($requestOptions['body']);

            $expected = sprintf('access_token=%s&data=[{"user_data":{"client_ip_address":"::1","client_user_agent":"Mozilla\/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit\/537.36 (KHTML, like Gecko) Chrome\/94.0.4606.61 Safari\/537.36"},"event_name":"ViewContent","event_time":%s,"custom_data":{"contents":[{"id":"Beige_strappy_summer_dress","quantity":1}],"content_ids":["Beige_strappy_summer_dress"],"content_name":"Beige strappy summer dress","content_type":"product"},"action_source":"website","event_source_url":"https:\/\/localhost:8000\/en_US\/products\/beige-strappy-summer-dress"}]', $this->accessToken, $eventTime);
            if (null !== $this->testEventCode) {
                $expected .= sprintf('&test_event_code=%s', $this->testEventCode);
            }

            self::assertSame($expected, $requestBody);
        }
    }

    private function getHttpClient(MockResponse $response): HttpClientInterface
    {
        if ($this->live) {
            return HttpClient::create();
        }

        return new MockHttpClient($response);
    }
}
