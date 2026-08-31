<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\EventSubscriber;

use Setono\SyliusFacebookPlugin\Event\CheckoutStartedEvent;
use Setono\SyliusFacebookPlugin\EventSubscriber\StartCheckoutSubscriber;
use Sylius\Component\Order\Context\CartContextInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class StartCheckoutSubscriberTest extends EventSubscriberTestCase
{
    /**
     * @test
     */
    public function it_subscribes_to_the_kernel_request_event(): void
    {
        self::assertSame([KernelEvents::REQUEST => 'track'], StartCheckoutSubscriber::getSubscribedEvents());
    }

    /**
     * @test
     */
    public function it_raises_a_checkout_started_event_when_the_checkout_starts_with_a_non_empty_cart(): void
    {
        $this->expectConversionsApiEvent(CheckoutStartedEvent::class);

        $this->createSubscriber($this->createOrder([$this->createOrderItem('JEANS_M', 1)]))
            ->track($this->createRequestEvent(['_route' => 'sylius_shop_checkout_start']));
    }

    /**
     * @test
     */
    public function it_does_nothing_when_the_cart_is_empty(): void
    {
        $this->expectNoConversionsApiEvent();

        $this->createSubscriber($this->createOrder([], true))
            ->track($this->createRequestEvent(['_route' => 'sylius_shop_checkout_start']));
    }

    /**
     * @test
     */
    public function it_does_nothing_on_other_routes_and_sub_requests(): void
    {
        $this->expectNoConversionsApiEvent();

        $subscriber = $this->createSubscriber($this->createOrder([$this->createOrderItem('JEANS_M', 1)]));
        $subscriber->track($this->createRequestEvent(['_route' => 'sylius_shop_cart_summary']));
        $subscriber->track($this->createRequestEvent(['_route' => 'sylius_shop_checkout_start'], null, HttpKernelInterface::SUB_REQUEST));
    }

    /**
     * @test
     */
    public function it_logs_an_error_instead_of_failing_when_the_cart_is_not_a_core_order(): void
    {
        $this->expectLoggedError('Expected an instance of Sylius\Component\Core\Model\OrderInterface');

        $this->createSubscriber($this->createNonCoreOrder())
            ->track($this->createRequestEvent(['_route' => 'sylius_shop_checkout_start']));
    }

    private function createSubscriber(BaseOrderInterface $cart): StartCheckoutSubscriber
    {
        $cartContext = $this->prophesize(CartContextInterface::class);
        $cartContext->getCart()->willReturn($cart);

        $subscriber = new StartCheckoutSubscriber($this->eventDispatcher->reveal(), $cartContext->reveal());
        $subscriber->setLogger($this->logger->reveal());

        return $subscriber;
    }
}
