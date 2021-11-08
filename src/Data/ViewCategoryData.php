<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Data;

use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

final class ViewCategoryData
{
    /** @var ProductInterface[] */
    private array $products;

    private ?TaxonInterface $taxon;

    /**
     * @param ProductInterface[] $products
     */
    public function __construct(array $products, TaxonInterface $taxon = null)
    {
        $this->products = $products;
        $this->taxon = $taxon;
    }

    /**
     * @return ProductInterface[]
     */
    public function getProducts(): array
    {
        return $this->products;
    }

    public function getTaxon(): ?TaxonInterface
    {
        return $this->taxon;
    }
}
