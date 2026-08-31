<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Event;

use Setono\MetaConversionsApi\Event\Event;
use Sylius\Component\Core\Model\ProductInterface;

final class ProductViewedEvent extends Event
{
    public function __construct(ProductInterface $product)
    {
        parent::__construct(self::EVENT_VIEW_CONTENT);

        $this->customData->contentType = 'product';
        $this->customData->contentName = (string) $product->getName();
        $this->customData->contentIds[] = (string) $product->getCode();
        $this->customData->contentCategory = $this->getTaxonName($product);
    }

    private function getTaxonName(ProductInterface $product): ?string
    {
        $taxon = $product->getMainTaxon() ?? $product->getTaxons()->first();

        return false === $taxon ? null : $taxon->getName();
    }
}
