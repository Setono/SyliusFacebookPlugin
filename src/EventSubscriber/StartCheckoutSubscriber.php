<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventSubscriber;

use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusFacebookPlugin\Event\CheckoutStartedEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Webmozart\Assert\Assert;

/**
 * @extends EventSubscriber<RequestEvent>
 */
final class StartCheckoutSubscriber extends EventSubscriber
{
    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        private readonly CartContextInterface $cartContext,
    ) {
        parent::__construct($eventDispatcher);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'track',
        ];
    }

    protected function callback(): callable
    {
        return function (RequestEvent $event): ?CheckoutStartedEvent {
            if (!$event->isMainRequest()) {
                return null;
            }

            $request = $event->getRequest();
            if ($request->attributes->get('_route') !== 'sylius_shop_checkout_start') {
                return null;
            }

            /** @var OrderInterface $order */
            $order = $this->cartContext->getCart();
            Assert::isInstanceOf($order, OrderInterface::class);

            if ($order->isEmpty()) {
                return null;
            }

            return new CheckoutStartedEvent($order);
        };
    }
}
