<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Client;

use Setono\SyliusFacebookPlugin\Model\PixelEventInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Webmozart\Assert\Assert;

final class Client implements ClientInterface
{
    private HttpClientInterface $httpClient;

    private string $apiVersion;

    private string $accessToken;

    private ?string $testEventCode;

    public function __construct(
        HttpClientInterface $httpClient,
        string $apiVersion,
        string $accessToken,
        ?string $testEventCode = null
    ) {
        $this->httpClient = $httpClient;
        $this->apiVersion = $apiVersion;
        $this->accessToken = $accessToken;
        $this->testEventCode = $testEventCode;
    }

    public function sendPixelEvent(PixelEventInterface $pixelEvent): int
    {
        $pixel = $pixelEvent->getPixel();
        Assert::notNull($pixel);

        $pixelId = $pixel->getPixelId();
        Assert::notNull($pixelId);

        $accessToken = $pixel->getCustomAccessToken() ?? $this->accessToken;

        $options = [
            'headers' => [
                'Content-Type' => 'application/x-www-form-urlencoded',
                'Accept' => 'application/json',
            ],
            'body' => [
                'access_token' => $accessToken,
                'data' => json_encode([
                    $pixelEvent->getData(),
                ]),
            ],
        ];

        if (null !== $this->testEventCode && '' !== $this->testEventCode) {
            $options['body']['test_event_code'] = $this->testEventCode;
        }

        $response = $this->httpClient->request(
            'POST',
            sprintf('https://graph.facebook.com/%s/%s/events', $this->apiVersion, $pixelId),
            $options
        );

        Assert::same($response->getStatusCode(), 200, $this->getErrorMessage($response));
        $content = $response->getContent(false);
        $json = json_decode($content, true);
        Assert::isArray($json);

        return (int) $json['events_received'];
    }

    private function getErrorMessage(ResponseInterface $response): string
    {
        $content = $response->getContent(false);
        $json = json_decode($content, true);
        Assert::isArray($json);

        $error = sprintf(
            'Wrong status code. Expected %s. Got: %s.',
            200,
            $response->getStatusCode()
        );

        if (array_key_exists('error', $json)) {
            /** @psalm-var array{message: string, error_subcode: int, error_user_msg: string} $errorPayload */
            $errorPayload = $json['error'];

            $error .= sprintf(
                ' Reason: %s [%s] %s',
                $errorPayload['error_subcode'],
                $errorPayload['message'],
                $errorPayload['error_user_msg']
            );
        }

        return $error;
    }
}
