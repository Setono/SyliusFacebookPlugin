<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\Event;

use Doctrine\Common\Collections\ArrayCollection;
use LogicException;
use Setono\MetaConversionsApi\Event\Content;
use Setono\MetaConversionsApi\Event\Event;
use Setono\SyliusFacebookPlugin\Event\ProductAddedToCartEvent;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class ProductAddedToCartEventTest extends OrderBasedEventTestCase
{
    /**
     * @test
     */
    public function it_populates_custom_data_from_the_added_order_item(): void
    {
        // The cart holds three units of the variant in total, but only two were added in this request
        $order = $this->createOrder([$this->createOrderItem('JEANS_M', 3, 1000, 3000)]);
        $addedOrderItem = $this->createAddedOrderItem('JEANS_M', 2, $this->createProduct($this->createTaxon('Clothes')));

        $event = new ProductAddedToCartEvent($order, $addedOrderItem);

        self::assertSame(Event::EVENT_ADD_TO_CART, $event->eventName);
        self::assertSame('USD', $event->customData->currency);
        self::assertSame(20.0, $event->customData->value);
        self::assertSame('product', $event->customData->contentType);
        self::assertSame('Jeans', $event->customData->contentName);
        self::assertSame(['JEANS'], $event->customData->contentIds);
        self::assertSame('Clothes', $event->customData->contentCategory);
        self::assertEquals([new Content('JEANS', 2, 10.0)], $event->customData->contents);
    }

    /**
     * @test
     */
    public function it_has_no_product_information_when_the_order_item_has_no_product(): void
    {
        $order = $this->createOrder([$this->createOrderItem('JEANS_M', 1, 1000, 1000)]);

        $event = new ProductAddedToCartEvent($order, $this->createAddedOrderItem('JEANS_M', 1, null));

        self::assertSame(10.0, $event->customData->value);
        self::assertNull($event->customData->contentName);
        self::assertSame([], $event->customData->contentIds);
        self::assertSame([], $event->customData->contents);
    }

    /**
     * @test
     */
    public function it_rounds_the_unit_price_to_the_nearest_cent(): void
    {
        // 1000 / 3 = 333.33 → rounds down to 333
        $order = $this->createOrder([$this->createOrderItem('JEANS_M', 3, 333, 1000)]);
        $event = new ProductAddedToCartEvent($order, $this->createAddedOrderItem('JEANS_M', 1, $this->createProduct()));

        self::assertSame(3.33, $event->customData->value);
        self::assertEquals([new Content('JEANS', 1, 3.33)], $event->customData->contents);

        // 2000 / 3 = 666.67 → rounds up to 667
        $order = $this->createOrder([$this->createOrderItem('JEANS_M', 3, 667, 2000)]);
        $event = new ProductAddedToCartEvent($order, $this->createAddedOrderItem('JEANS_M', 1, $this->createProduct()));

        self::assertSame(6.67, $event->customData->value);
        self::assertEquals([new Content('JEANS', 1, 6.67)], $event->customData->contents);
    }

    /**
     * @test
     */
    public function it_requires_the_added_order_item_to_have_a_variant(): void
    {
        $order = $this->createOrder([$this->createOrderItem('JEANS_M', 1, 1000, 1000)]);

        $addedOrderItem = $this->prophesize(OrderItemInterface::class);
        $addedOrderItem->getVariant()->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        new ProductAddedToCartEvent($order, $addedOrderItem->reveal());
    }

    /**
     * @test
     */
    public function it_requires_the_added_order_item_variant_to_have_a_code(): void
    {
        $order = $this->createOrder([$this->createOrderItem('JEANS_M', 1, 1000, 1000)]);

        $addedOrderItem = $this->prophesize(OrderItemInterface::class);
        $addedOrderItem->getVariant()->willReturn($this->createProductVariant(null));

        $this->expectException(\InvalidArgumentException::class);

        new ProductAddedToCartEvent($order, $addedOrderItem->reveal());
    }

    /**
     * @test
     */
    public function it_requires_the_order_items_to_have_a_variant(): void
    {
        $orderItem = $this->prophesize(OrderItemInterface::class);
        $orderItem->getVariant()->willReturn(null);

        $this->expectException(\InvalidArgumentException::class);

        new ProductAddedToCartEvent($this->createOrder([$orderItem->reveal()]), $this->createAddedOrderItem('JEANS_M', 1, $this->createProduct()));
    }

    /**
     * @test
     */
    public function it_throws_when_the_added_order_item_is_not_on_the_order(): void
    {
        $order = $this->createOrder([$this->createOrderItem('T_SHIRT_L', 1, 500, 500)]);

        $this->expectException(LogicException::class);

        new ProductAddedToCartEvent($order, $this->createAddedOrderItem('JEANS_M', 1, $this->createProduct()));
    }

    /**
     * @test
     */
    public function it_falls_back_to_the_first_taxon_when_the_product_has_no_main_taxon(): void
    {
        $order = $this->createOrder([$this->createOrderItem('JEANS_M', 1, 1000, 1000)]);
        $product = $this->createProduct(null, [$this->createTaxon('Clothes'), $this->createTaxon('Jeans')]);

        $event = new ProductAddedToCartEvent($order, $this->createAddedOrderItem('JEANS_M', 1, $product));

        self::assertSame('Clothes', $event->customData->contentCategory);
    }

    /**
     * @test
     */
    public function it_has_no_content_category_when_the_product_has_no_taxons(): void
    {
        $order = $this->createOrder([$this->createOrderItem('JEANS_M', 1, 1000, 1000)]);

        $event = new ProductAddedToCartEvent($order, $this->createAddedOrderItem('JEANS_M', 1, $this->createProduct()));

        self::assertNull($event->customData->contentCategory);
    }

    private function createAddedOrderItem(string $variantCode, int $quantity, ?ProductInterface $product): OrderItemInterface
    {
        $orderItem = $this->prophesize(OrderItemInterface::class);
        $orderItem->getVariant()->willReturn($this->createProductVariant($variantCode));
        $orderItem->getQuantity()->willReturn($quantity);
        $orderItem->getProduct()->willReturn($product);

        return $orderItem->reveal();
    }

    /**
     * @param list<TaxonInterface> $taxons
     */
    private function createProduct(?TaxonInterface $mainTaxon = null, array $taxons = []): ProductInterface
    {
        $product = $this->prophesize(ProductInterface::class);
        $product->getName()->willReturn('Jeans');
        $product->getCode()->willReturn('JEANS');
        $product->getMainTaxon()->willReturn($mainTaxon);
        $product->getTaxons()->willReturn(new ArrayCollection($taxons));

        return $product->reveal();
    }

    private function createTaxon(string $name): TaxonInterface
    {
        $taxon = $this->prophesize(TaxonInterface::class);
        $taxon->getName()->willReturn($name);

        return $taxon->reveal();
    }
}
