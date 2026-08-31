<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\EventSubscriber;

use Setono\SyliusFacebookPlugin\Event\ProductAddedToCartEvent;
use Setono\SyliusFacebookPlugin\EventSubscriber\AddToCartSubscriber;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;

final class AddToCartSubscriberTest extends EventSubscriberTestCase
{
    /**
     * @test
     */
    public function it_subscribes_to_the_order_item_post_add_event(): void
    {
        self::assertSame(['sylius.order_item.post_add' => 'track'], AddToCartSubscriber::getSubscribedEvents());
    }

    /**
     * @test
     */
    public function it_raises_a_product_added_to_cart_event(): void
    {
        $this->expectConversionsApiEvent(ProductAddedToCartEvent::class);

        $cart = $this->createOrder([$this->createOrderItem('JEANS_M', 2, $this->createProduct())]);
        $addedOrderItem = $this->createOrderItem('JEANS_M', 1, $this->createProduct());

        $this->createSubscriber($cart)->track(new ResourceControllerEvent($addedOrderItem));
    }

    /**
     * @test
     */
    public function it_logs_an_error_instead_of_failing_when_the_subject_is_not_an_order_item(): void
    {
        $this->expectLoggedError('Expected an instance of Sylius\Component\Core\Model\OrderItemInterface');

        $this->createSubscriber($this->createOrder())->track(new ResourceControllerEvent(new \stdClass()));
    }

    /**
     * @test
     */
    public function it_logs_an_error_instead_of_failing_when_the_cart_is_not_a_core_order(): void
    {
        $this->expectLoggedError('Expected an instance of Sylius\Component\Core\Model\OrderInterface');

        $this->createSubscriber($this->createNonCoreOrder())->track(new ResourceControllerEvent($this->createOrderItem('JEANS_M', 1)));
    }

    private function createSubscriber(BaseOrderInterface $cart): AddToCartSubscriber
    {
        $cartContext = $this->prophesize(CartContextInterface::class);
        $cartContext->getCart()->willReturn($cart);

        $subscriber = new AddToCartSubscriber($this->eventDispatcher->reveal(), $cartContext->reveal());
        $subscriber->setLogger($this->logger->reveal());

        return $subscriber;
    }
}
