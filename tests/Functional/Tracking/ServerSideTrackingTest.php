<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Functional\Tracking;

use Setono\MetaConversionsApi\Event\Event;
use Setono\MetaConversionsApi\Pixel\Pixel;
use Setono\MetaConversionsApiBundle\Event\ConversionsApiEventRaised;
use Setono\MetaConversionsApiBundle\Message\Command\SendEvent;
use Setono\MetaConversionsApiBundle\Message\Handler\SendEventHandler;
use Setono\SyliusFacebookPlugin\Tests\Functional\FunctionalTestCase;
use Setono\SyliusFacebookPlugin\Tests\Functional\Http\RecordingResponseFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * Covers the whole way from a raised event to the request that is sent to Meta, with the pixels coming from the
 * database. Everything in between belongs to the Meta Conversions API bundle, and nothing in it fails loudly when the
 * plugin is wired wrongly: the event is just dropped
 */
final class ServerSideTrackingTest extends FunctionalTestCase
{
    /**
     * @test
     */
    public function it_sends_an_event_to_the_pixels_of_the_channel_the_event_was_raised_on(): void
    {
        $web = self::createChannel('WEB');
        $web->setHostname('web.example.com');
        $mobile = self::createChannel('MOBILE');
        $mobile->setHostname('mobile.example.com');
        self::persist($web, $mobile);

        self::createPixel('1000', true, $web);
        self::createPixel('2000', false, $web);
        self::createPixel('3000', true, $mobile);

        $requestStack = static::getContainer()->get('request_stack');
        self::assertInstanceOf(RequestStack::class, $requestStack);
        $requestStack->push(Request::create('https://web.example.com/products/blue-jeans', 'GET', [], [], [], [
            // The bundle drops events from bots, and an empty user agent counts as one
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/126.0.0.0 Safari/537.36',
            'REMOTE_ADDR' => '203.0.113.10',
        ]));

        $eventDispatcher = static::getContainer()->get('event_dispatcher');
        self::assertInstanceOf(EventDispatcherInterface::class, $eventDispatcher);
        $eventDispatcher->dispatch(new ConversionsApiEventRaised(new Event(Event::EVENT_VIEW_CONTENT)));

        $requests = self::getRecordedRequests();
        self::assertCount(1, $requests);
        self::assertSame('POST', $requests[0]['method']);
        self::assertSame('1000', self::pixelId($requests[0]['url']));

        // Meta retired Graph API v20.0 on 24 September 2026
        self::assertGreaterThanOrEqual(21.0, self::graphApiVersion($requests[0]['url']));

        $fields = self::fields($requests[0]['body']);
        self::assertSame('access_token', $fields['access_token']);
        self::assertSame('ViewContent', $fields['event']['event_name'] ?? null);
        self::assertSame('https://web.example.com/products/blue-jeans', $fields['event']['event_source_url'] ?? null);
    }

    /**
     * When the command is routed to a transport it is handled by a worker, where there is no request and hence no
     * channel. The access tokens never travel with the command, so the handler gets them from the plugin
     *
     * @test
     */
    public function it_adds_the_access_token_back_when_the_event_is_sent_outside_of_a_request(): void
    {
        $web = self::createChannel('WEB');
        $mobile = self::createChannel('MOBILE');

        self::createPixel('1000', true, $web);
        self::createPixel('3000', true, $mobile);

        $event = new Event(Event::EVENT_PURCHASE);
        $event->pixels = [new Pixel('3000', 'access_token')];

        $message = SendEvent::fromEvent($event);
        self::assertNull($message->preparedEvent->pixels[0]->accessToken);

        $handler = static::getContainer()->get(SendEventHandler::class);
        self::assertInstanceOf(SendEventHandler::class, $handler);
        $handler($message);

        $requests = self::getRecordedRequests();
        self::assertCount(1, $requests);
        self::assertSame('3000', self::pixelId($requests[0]['url']));
        self::assertSame('access_token', self::fields($requests[0]['body'])['access_token']);
    }

    /**
     * @return list<array{method: string, url: string, body: string}>
     */
    private static function getRecordedRequests(): array
    {
        $responseFactory = static::getContainer()->get(RecordingResponseFactory::class);
        self::assertInstanceOf(RecordingResponseFactory::class, $responseFactory);

        return $responseFactory->requests;
    }

    private static function pixelId(string $url): string
    {
        self::assertMatchesRegularExpression('#^https://graph\.facebook\.com/v\d+\.\d+/(\d+)/events$#', $url);

        return (string) preg_replace('#^.+/(\d+)/events$#', '$1', $url);
    }

    private static function graphApiVersion(string $url): float
    {
        return (float) preg_replace('#^https://graph\.facebook\.com/v(\d+\.\d+)/.+$#', '$1', $url);
    }

    /**
     * @return array{access_token: string, event: array<array-key, mixed>}
     */
    private static function fields(string $body): array
    {
        parse_str($body, $fields);

        $accessToken = $fields['access_token'] ?? null;
        self::assertIsString($accessToken);

        $data = $fields['data'] ?? null;
        self::assertIsString($data);

        $events = json_decode($data, true, 512, \JSON_THROW_ON_ERROR);
        self::assertIsArray($events);
        self::assertCount(1, $events);
        self::assertIsArray($events[0]);

        return ['access_token' => $accessToken, 'event' => $events[0]];
    }
}
