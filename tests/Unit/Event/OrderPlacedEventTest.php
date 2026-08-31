<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\Event;

use Setono\MetaConversionsApi\Event\Content;
use Setono\MetaConversionsApi\Event\Event;
use Setono\SyliusFacebookPlugin\Event\OrderPlacedEvent;

final class OrderPlacedEventTest extends OrderBasedEventTestCase
{
    /**
     * @test
     */
    public function it_populates_custom_data_and_user_data_from_the_order(): void
    {
        $order = $this->createOrder(
            [
                $this->createOrderItem('JEANS_M', 2, 1000, 2000),
                $this->createOrderItem('T_SHIRT_L', 1, 500, 500),
            ],
            2500,
            $this->createCustomer('m'),
            $this->createBillingAddress(),
        );

        $event = new OrderPlacedEvent($order);

        self::assertSame(Event::EVENT_PURCHASE, $event->eventName);
        self::assertSame('USD', $event->customData->currency);
        self::assertSame(25.0, $event->customData->value);
        self::assertSame('product', $event->customData->contentType);
        self::assertSame(['JEANS_M', 'T_SHIRT_L'], $event->customData->contentIds);
        self::assertEquals([new Content('JEANS_M', 2, 10.0), new Content('T_SHIRT_L', 1, 5.0)], $event->customData->contents);
        self::assertSame(3, $event->customData->numItems);

        self::assertSame(['john.doe@example.com'], $event->userData->email);
        self::assertSame(['+4512345678', '+4587654321'], $event->userData->phoneNumber);
        self::assertSame(['m'], $event->userData->gender);
        self::assertSame(['John'], $event->userData->firstName);
        self::assertSame(['Doe'], $event->userData->lastName);
        self::assertSame(['8000'], $event->userData->zipCode);
        self::assertSame(['Aarhus'], $event->userData->city);
        self::assertSame(['DK'], $event->userData->country);
    }

    /**
     * @test
     */
    public function it_accepts_both_known_genders(): void
    {
        self::assertSame(['f'], (new OrderPlacedEvent($this->createOrder([], 0, $this->createCustomer('f'))))->userData->gender);
        self::assertSame(['m'], (new OrderPlacedEvent($this->createOrder([], 0, $this->createCustomer('m'))))->userData->gender);
    }

    /**
     * @test
     */
    public function it_ignores_unknown_genders(): void
    {
        $event = new OrderPlacedEvent($this->createOrder([], 0, $this->createCustomer('u')));

        self::assertSame([], $event->userData->gender);
        self::assertSame(['john.doe@example.com'], $event->userData->email);
    }

    /**
     * @test
     */
    public function it_skips_items_without_a_variant(): void
    {
        $orderItem = $this->prophesize(\Sylius\Component\Core\Model\OrderItemInterface::class);
        $orderItem->getVariant()->willReturn(null);
        $orderItem->getQuantity()->willReturn(2);

        $event = new OrderPlacedEvent($this->createOrder([$orderItem->reveal(), $this->createOrderItem('JEANS_M', 1, 1000, 1000)], 1000));

        self::assertSame(['JEANS_M'], $event->customData->contentIds);
        self::assertEquals([new Content('JEANS_M', 1, 10.0)], $event->customData->contents);
        self::assertSame(3, $event->customData->numItems);
    }

    /**
     * @test
     */
    public function it_skips_items_without_a_variant_code_and_orders_without_customer_and_billing_address(): void
    {
        $orderItem = $this->prophesize(\Sylius\Component\Core\Model\OrderItemInterface::class);
        $orderItem->getVariant()->willReturn($this->createProductVariant(null));
        $orderItem->getQuantity()->willReturn(1);
        $orderItem->getDiscountedUnitPrice()->willReturn(100);

        $event = new OrderPlacedEvent($this->createOrder([$orderItem->reveal()], 100));

        self::assertSame(1.0, $event->customData->value);
        self::assertSame([], $event->customData->contentIds);
        self::assertEquals([new Content('', 1, 1.0)], $event->customData->contents);
        self::assertSame(1, $event->customData->numItems);
        self::assertSame([], $event->userData->email);
        self::assertSame([], $event->userData->firstName);
    }
}
