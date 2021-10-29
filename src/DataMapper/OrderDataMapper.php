<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\DataMapper;

use FacebookAds\Object\ServerSide\Content;
use Setono\SyliusFacebookPlugin\Formatter\MoneyFormatterInterface;
use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Webmozart\Assert\Assert;

final class OrderDataMapper implements DataMapperInterface
{
    private MoneyFormatterInterface $moneyFormatter;

    public function __construct(MoneyFormatterInterface $moneyFormatter)
    {
        $this->moneyFormatter = $moneyFormatter;
    }

    /**
     * @psalm-assert-if-true OrderInterface $source
     */
    public function supports($source, ServerSideEventInterface $target, array $context = []): bool
    {
        return $source instanceof OrderInterface;
    }

    /**
     * @param OrderInterface $source
     */
    public function map($source, ServerSideEventInterface $target, array $context = []): void
    {
        $customData = $target->getCustomData();
        $customData->setValue(
            $this->moneyFormatter->format($source->getTotal())
        );
        $customData->setCurrency($source->getCurrencyCode());
        $customData->setContentIds($this->getContentIds($source));
        $customData->setContents($this->getContents($source));
        $customData->setContentType(ServerSideEventInterface::CONTENT_TYPE_PRODUCT);
    }

    private function getContentIds(OrderInterface $order): array
    {
        return array_filter($order->getItems()->map(function (OrderItemInterface $orderItem): ?string {
            $variant = $orderItem->getVariant();
            if (null === $variant) {
                return null;
            }

            return $variant->getCode();
        })->toArray());
    }

    /**
     * @returns array|Content[]
     */
    private function getContents(OrderInterface $order): array
    {
        return array_filter($order->getItems()->map(function (OrderItemInterface $orderItem): ?Content {
            $variant = $orderItem->getVariant();
            if (null === $variant) {
                return null;
            }

            return (new Content())
                ->setProductId($variant->getCode())
                ->setQuantity($orderItem->getQuantity())
                ->setItemPrice(
                    $this->moneyFormatter->format($orderItem->getDiscountedUnitPrice())
                );
        })->toArray());
    }
}
