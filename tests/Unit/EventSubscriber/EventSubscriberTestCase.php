<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\EventSubscriber;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;
use Setono\MetaConversionsApi\Event\Event;
use Setono\MetaConversionsApiBundle\Event\ConversionsApiEventRaised;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Order\Model\OrderInterface as BaseOrderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

abstract class EventSubscriberTestCase extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<EventDispatcherInterface> */
    protected ObjectProphecy $eventDispatcher;

    /** @var ObjectProphecy<LoggerInterface> */
    protected ObjectProphecy $logger;

    protected function setUp(): void
    {
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
    }

    /**
     * @param class-string<Event> $eventClass
     */
    protected function expectConversionsApiEvent(string $eventClass): void
    {
        $this->eventDispatcher
            ->dispatch(Argument::that(static fn (object $event): bool => $event instanceof ConversionsApiEventRaised && $event->event instanceof $eventClass))
            ->shouldBeCalledOnce()
            ->willReturnArgument(0)
        ;
        $this->logger->error(Argument::cetera())->shouldNotBeCalled();
    }

    protected function expectNoConversionsApiEvent(): void
    {
        $this->eventDispatcher->dispatch(Argument::any())->shouldNotBeCalled();
        $this->logger->error(Argument::cetera())->shouldNotBeCalled();
    }

    protected function expectLoggedError(string $messageContaining): void
    {
        $this->eventDispatcher->dispatch(Argument::any())->shouldNotBeCalled();
        $this->logger->error(Argument::containingString($messageContaining))->shouldBeCalledOnce();
    }

    /**
     * Returns an order that is not a Sylius core order
     */
    protected function createNonCoreOrder(): BaseOrderInterface
    {
        $order = $this->prophesize(BaseOrderInterface::class);
        $order->isEmpty()->willReturn(false);

        return $order->reveal();
    }

    /**
     * @param array<string, mixed> $attributes
     */
    protected function createRequestEvent(array $attributes, ?Session $session = null, int $requestType = HttpKernelInterface::MAIN_REQUEST): RequestEvent
    {
        $request = new Request([], [], $attributes);
        if (null !== $session) {
            $request->setSession($session);
        }

        return new RequestEvent($this->prophesize(HttpKernelInterface::class)->reveal(), $request, $requestType);
    }

    /**
     * @param array<string, mixed> $values
     */
    protected function createSession(array $values = []): Session
    {
        $session = new Session(new MockArraySessionStorage());
        foreach ($values as $key => $value) {
            $session->set($key, $value);
        }

        return $session;
    }

    /**
     * @param list<OrderItemInterface> $items
     */
    protected function createOrder(array $items = [], bool $empty = false): OrderInterface
    {
        $order = $this->prophesize(OrderInterface::class);
        $order->getCurrencyCode()->willReturn('USD');
        $order->getTotal()->willReturn(1000);
        $order->getItems()->willReturn(new ArrayCollection($items));
        $order->isEmpty()->willReturn($empty);
        $order->getCustomer()->willReturn(null);
        $order->getBillingAddress()->willReturn(null);

        return $order->reveal();
    }

    protected function createOrderItem(string $variantCode, int $quantity, ?ProductInterface $product = null): OrderItemInterface
    {
        $variant = $this->prophesize(ProductVariantInterface::class);
        $variant->getCode()->willReturn($variantCode);

        $orderItem = $this->prophesize(OrderItemInterface::class);
        $orderItem->getVariant()->willReturn($variant->reveal());
        $orderItem->getQuantity()->willReturn($quantity);
        $orderItem->getDiscountedUnitPrice()->willReturn(1000);
        $orderItem->getTotal()->willReturn(1000 * $quantity);
        $orderItem->getProduct()->willReturn($product);

        return $orderItem->reveal();
    }

    protected function createProduct(): ProductInterface
    {
        $product = $this->prophesize(ProductInterface::class);
        $product->getName()->willReturn('Jeans');
        $product->getCode()->willReturn('JEANS');
        $product->getMainTaxon()->willReturn(null);
        $product->getTaxons()->willReturn(new ArrayCollection([]));

        return $product->reveal();
    }
}
