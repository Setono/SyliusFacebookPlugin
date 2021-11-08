<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\DataMapper;

use FacebookAds\Object\ServerSide\Content;
use Setono\SyliusFacebookPlugin\Formatter\MoneyFormatterInterface;
use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use Sylius\Component\Core\Model\OrderInterface as CoreOrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface as CoreOrderItemInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Webmozart\Assert\Assert;

/** not final */ class OrderDataMapper implements DataMapperInterface
{
    protected MoneyFormatterInterface $moneyFormatter;

    public function __construct(MoneyFormatterInterface $moneyFormatter)
    {
        $this->moneyFormatter = $moneyFormatter;
    }

    /**
     * @psalm-assert-if-true OrderInterface $source
     */
    public function supports(object $source, ServerSideEventInterface $target, array $context = []): bool
    {
        return $source instanceof OrderInterface;
    }

    public function map(object $source, ServerSideEventInterface $target, array $context = []): void
    {
        Assert::true($this->supports($source, $target, $context));

        if (!$source instanceof CoreOrderInterface) {
            return;
        }

        $customData = $target->getCustomData();
        /** @psalm-suppress PossiblyNullArgument */
        $customData->setValue(
            $this->moneyFormatter->format($source->getTotal())
        );

        /** @psalm-suppress PossiblyNullArgument */
        $customData->setCurrency($source->getCurrencyCode());

        $customData->setContentIds($this->getContentIds($source));
        $customData->setContents($this->getContents($source));
        $customData->setContentType(ServerSideEventInterface::CONTENT_TYPE_PRODUCT);
    }

    /**
     * @psalm-return array<array-key, string>
     */
    protected function getContentIds(OrderInterface $order): array
    {
        return array_filter($order->getItems()->map(function (OrderItemInterface $orderItem): ?string {
            if (!$orderItem instanceof CoreOrderItemInterface) {
                return null;
            }

            $variant = $orderItem->getVariant();
            if (null === $variant) {
                return null;
            }

            return $variant->getCode();
        })->toArray());
    }

    /**
     * @return array<array-key, Content>
     */
    protected function getContents(OrderInterface $order): array
    {
        /** @var array<array-key, Content> $contents */
        $contents = array_filter($order->getItems()->map(function (OrderItemInterface $orderItem): ?Content {
            if (!$orderItem instanceof CoreOrderItemInterface) {
                return null;
            }

            $variant = $orderItem->getVariant();
            if (null === $variant) {
                return null;
            }

            $contentId = $variant->getCode();
            if (null === $contentId) {
                return null;
            }

            $content = new Content();
            $content->setProductId($contentId);
            $content->setQuantity($orderItem->getQuantity());

            /** @psalm-suppress PossiblyNullArgument */
            $content->setItemPrice(
                $this->moneyFormatter->format($orderItem->getDiscountedUnitPrice())
            );

            return $content;
        })->toArray());

        return $contents;
    }
}
