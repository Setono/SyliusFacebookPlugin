<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\Event;

use Setono\MetaConversionsApi\Event\Event;
use Setono\SyliusFacebookPlugin\Event\CheckoutStartedEvent;

final class CheckoutStartedEventTest extends OrderBasedEventTestCase
{
    /**
     * @test
     */
    public function it_is_an_initiate_checkout_event_populated_from_the_order(): void
    {
        $event = new CheckoutStartedEvent($this->createOrder([$this->createOrderItem('JEANS_M', 3, 1000, 3000)], 3000));

        self::assertSame(Event::EVENT_INITIATE_CHECKOUT, $event->eventName);
        self::assertSame('USD', $event->customData->currency);
        self::assertSame(30.0, $event->customData->value);
        self::assertSame(['JEANS_M'], $event->customData->contentIds);
        self::assertSame(3, $event->customData->numItems);
    }
}
