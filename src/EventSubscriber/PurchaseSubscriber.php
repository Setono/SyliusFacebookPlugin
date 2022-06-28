<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventSubscriber;

use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusFacebookPlugin\Event\OrderPlacedEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class PurchaseSubscriber extends EventSubscriber
{
    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($eventDispatcher);

        $this->orderRepository = $orderRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'track',
        ];
    }

    protected function callback(): callable
    {
        return function (RequestEvent $event): ?OrderPlacedEvent {
            if (!$event->isMainRequest()) {
                return null;
            }

            $request = $event->getRequest();

            if ($request->attributes->get('_route') !== 'sylius_shop_order_thank_you') {
                return null;
            }

            $order = $this->resolveOrder($request);
            if (null === $order) {
                return null;
            }

            return new OrderPlacedEvent($order);
        };
    }

    /**
     * This method will return an OrderInterface if
     * - A session exists with the order id
     * - The order can be found in the order repository
     */
    private function resolveOrder(Request $request): ?OrderInterface
    {
        $orderId = $request->getSession()->get('sylius_order_id');

        if (!is_scalar($orderId)) {
            return null;
        }

        $order = $this->orderRepository->find($orderId);
        if (!$order instanceof OrderInterface) {
            return null;
        }

        return $order;
    }
}
