<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Event;

use Sylius\Component\Core\Model\OrderInterface;

final class CheckoutStartedEvent extends OrderBasedEvent
{
    public function __construct(OrderInterface $order)
    {
        parent::__construct($order, self::EVENT_INITIATE_CHECKOUT);
    }
}
