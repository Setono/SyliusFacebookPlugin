<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Client;

use Setono\SyliusFacebookPlugin\Model\PixelEventInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Webmozart\Assert\Assert;

final class Client implements ClientInterface
{
    private HttpClientInterface $httpClient;

    private string $apiVersion;

    private string $accessToken;

    public function __construct(
        HttpClientInterface $httpClient,
        string $apiVersion,
        string $accessToken
    ) {
        $this->httpClient = $httpClient;
        $this->apiVersion = $apiVersion;
        $this->accessToken = $accessToken;
    }

    public function sendPixelEvent(PixelEventInterface $pixelEvent): int
    {
        $pixel = $pixelEvent->getPixel();
        Assert::notNull($pixel);

        $pixelId = $pixel->getPixelId();
        Assert::notNull($pixelId);

        $response = $this->httpClient->request(
            'POST',
            sprintf('https://graph.facebook.com/%s/%s/events', $this->apiVersion, $pixelId),
            [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'Accept' => 'application/json',
                ],
                'body' => [
                    'access_token' => $this->accessToken,
                    'data' => json_encode([
                        $pixelEvent->getData(),
                    ]),
                ],
            ]
        );

        Assert::same($response->getStatusCode(), 200);
        $content = $response->getContent();
        $json = json_decode($content, true);
        Assert::isArray($json);

        return (int) $json['events_received'];
    }
}
