<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\EventSubscriber;

use Prophecy\Prophecy\ObjectProphecy;
use Setono\SyliusFacebookPlugin\Event\OrderPlacedEvent;
use Setono\SyliusFacebookPlugin\EventSubscriber\PurchaseSubscriber;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;

final class PurchaseSubscriberTest extends EventSubscriberTestCase
{
    /** @var ObjectProphecy<OrderRepositoryInterface<OrderInterface>> */
    private ObjectProphecy $orderRepository;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ObjectProphecy<OrderRepositoryInterface<OrderInterface>> $orderRepository */
        $orderRepository = $this->prophesize(OrderRepositoryInterface::class);
        $this->orderRepository = $orderRepository;
    }

    /**
     * @test
     */
    public function it_subscribes_to_the_kernel_request_event(): void
    {
        self::assertSame([KernelEvents::REQUEST => 'track'], PurchaseSubscriber::getSubscribedEvents());
    }

    /**
     * @test
     */
    public function it_raises_an_order_placed_event_on_the_thank_you_page(): void
    {
        $this->expectConversionsApiEvent(OrderPlacedEvent::class);
        $this->orderRepository->find(42)->willReturn($this->createOrder());

        $this->createSubscriber()->track($this->createRequestEvent(
            ['_route' => 'sylius_shop_order_thank_you'],
            $this->createSession(['sylius_order_id' => 42]),
        ));
    }

    /**
     * @test
     */
    public function it_does_nothing_for_sub_requests(): void
    {
        $this->expectNoConversionsApiEvent();

        $this->createSubscriber()->track($this->createRequestEvent(
            ['_route' => 'sylius_shop_order_thank_you'],
            $this->createSession(['sylius_order_id' => 42]),
            HttpKernelInterface::SUB_REQUEST,
        ));
    }

    /**
     * @test
     */
    public function it_does_nothing_on_other_routes(): void
    {
        $this->expectNoConversionsApiEvent();

        $this->createSubscriber()->track($this->createRequestEvent(
            ['_route' => 'sylius_shop_homepage'],
            $this->createSession(['sylius_order_id' => 42]),
        ));
    }

    /**
     * @test
     */
    public function it_does_nothing_when_the_session_has_no_order_id(): void
    {
        $this->expectNoConversionsApiEvent();

        $this->createSubscriber()->track($this->createRequestEvent(
            ['_route' => 'sylius_shop_order_thank_you'],
            $this->createSession(),
        ));
    }

    /**
     * @test
     */
    public function it_does_nothing_when_the_order_does_not_exist(): void
    {
        $this->expectNoConversionsApiEvent();
        $this->orderRepository->find(42)->willReturn(null);

        $this->createSubscriber()->track($this->createRequestEvent(
            ['_route' => 'sylius_shop_order_thank_you'],
            $this->createSession(['sylius_order_id' => 42]),
        ));
    }

    /**
     * @test
     */
    public function it_does_nothing_when_the_order_is_not_a_core_order(): void
    {
        $this->expectNoConversionsApiEvent();
        $this->orderRepository->find(42)->willReturn($this->createNonCoreOrder());

        $this->createSubscriber()->track($this->createRequestEvent(
            ['_route' => 'sylius_shop_order_thank_you'],
            $this->createSession(['sylius_order_id' => 42]),
        ));
    }

    private function createSubscriber(): PurchaseSubscriber
    {
        $subscriber = new PurchaseSubscriber($this->eventDispatcher->reveal(), $this->orderRepository->reveal());
        $subscriber->setLogger($this->logger->reveal());

        return $subscriber;
    }
}
