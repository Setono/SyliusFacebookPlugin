<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Event;

use LogicException;
use Setono\MetaConversionsApi\Event\Content;
use Setono\MetaConversionsApi\Event\Event;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Webmozart\Assert\Assert;

final class ProductAddedToCartEvent extends Event
{
    use FormatAmountTrait;

    /**
     * Why do we need to inject both the order and order item? Because there is a bug in Sylius (https://github.com/Sylius/Sylius/issues/9407)
     * where the order item doesn't hold the presumed information. This includes the reference to the actual order
     * and the total (and other prices) on the order item
     *
     * @param OrderInterface $order the order after the item was added
     * @param OrderItemInterface $addedOrderItem the actual item that was added
     */
    public function __construct(OrderInterface $order, OrderItemInterface $addedOrderItem)
    {
        parent::__construct(self::EVENT_ADD_TO_CART);

        $orderItem = $this->findOrderItem($order, $addedOrderItem);

        $this->customData->value = self::formatAmount(
            (int) round(($orderItem->getTotal() / $orderItem->getQuantity()) * $addedOrderItem->getQuantity())
        );
        $this->customData->currency = $order->getCurrencyCode();

        $product = $addedOrderItem->getProduct();
        if (null !== $product) {
            $this->populateProductInformation($product);

            $this->customData->contents[] = new Content(
                (string) $product->getCode(),
                $addedOrderItem->getQuantity(),
                self::formatAmount((int) round($orderItem->getTotal() / $orderItem->getQuantity()))
            );
        }
    }

    private function findOrderItem(OrderInterface $order, OrderItemInterface $addedOrderItem): OrderItemInterface
    {
        $addedVariant = $addedOrderItem->getVariant();
        Assert::notNull($addedVariant);

        $addedVariantCode = $addedVariant->getCode();
        Assert::notNull($addedVariantCode);

        foreach ($order->getItems() as $orderItem) {
            $variant = $orderItem->getVariant();
            Assert::notNull($variant);

            if ($variant->getCode() === $addedVariantCode) {
                return $orderItem;
            }
        }

        throw new LogicException('Could not find given order item on the actual order');
    }

    private function populateProductInformation(ProductInterface $product): void
    {
        $this->customData->contentType = 'product';
        $this->customData->contentName = (string) $product->getName();
        $this->customData->contentIds[] = (string) $product->getCode();
        $this->customData->contentCategory = $this->getTaxonName($product);
    }

    private function getTaxonName(ProductInterface $product): ?string
    {
        $taxon = $product->getMainTaxon();
        if (null !== $taxon) {
            return $taxon->getName();
        }

        $taxons = $product->getTaxons();
        if ($taxons->isEmpty()) {
            return null;
        }

        /** @var mixed|TaxonInterface $taxon */
        $taxon = $taxons->first();
        Assert::isInstanceOf($taxon, TaxonInterface::class);

        return $taxon->getName();
    }
}
