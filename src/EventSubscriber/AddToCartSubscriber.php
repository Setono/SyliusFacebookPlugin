<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventSubscriber;

use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusFacebookPlugin\Event\ProductAddedToCartEvent;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Webmozart\Assert\Assert;

final class AddToCartSubscriber extends EventSubscriber
{
    private CartContextInterface $cartContext;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        CartContextInterface $cartContext
    ) {
        parent::__construct($eventDispatcher);

        $this->cartContext = $cartContext;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.order_item.post_add' => 'track',
        ];
    }

    protected function callback(): callable
    {
        return function (ResourceControllerEvent $event): ProductAddedToCartEvent {
            /** @var mixed|OrderItemInterface $orderItem */
            $orderItem = $event->getSubject();
            Assert::isInstanceOf($orderItem, OrderItemInterface::class);

            /** @var OrderInterface $order */
            $order = $this->cartContext->getCart();
            Assert::isInstanceOf($order, OrderInterface::class);

            return new ProductAddedToCartEvent($order, $orderItem);
        };
    }
}
