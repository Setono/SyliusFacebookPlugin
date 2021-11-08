<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\DataMapper;

use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use Sylius\Component\Core\Model\OrderInterface as CoreOrderInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Webmozart\Assert\Assert;

final class CheckoutInitiatedOrderDataMapper implements DataMapperInterface
{
    /**
     * @psalm-assert-if-true OrderInterface $source
     */
    public function supports(object $source, ServerSideEventInterface $target, array $context = []): bool
    {
        return $source instanceof OrderInterface
            && isset($context['event'])
            && ServerSideEventInterface::EVENT_INITIATE_CHECKOUT === $context['event'];
    }

    public function map(object $source, ServerSideEventInterface $target, array $context = []): void
    {
        Assert::true($this->supports($source, $target, $context));

        if (!$source instanceof CoreOrderInterface) {
            return;
        }

        $customData = $target->getCustomData();
        $customData->setNumItems(
            (string) $this->getNumItems($source)
        );
    }

    private function getNumItems(OrderInterface $order): int
    {
        return array_sum($order->getItems()->map(function (OrderItemInterface $orderItem): int {
            return $orderItem->getQuantity();
        })->toArray());
    }
}
