<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Event;

use Setono\MetaConversionsApi\Event\Event;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

/**
 * See https://developers.facebook.com/docs/marketing-api/audiences/guides/dynamic-product-audiences/#setuppixel
 * for reference of the 'ViewCategory' custom event
 */
final class CategoryViewedEvent extends Event
{
    /**
     * @param list<string> $products list of top 5-10 products (codes) in this category
     */
    public function __construct(TaxonInterface $taxon, array $products = [])
    {
        parent::__construct('ViewCategory');

        $this->customData->contentType = 'product';
        $this->customData->contentName = (string) $taxon->getName();
        $this->customData->contentIds = $products;
        $this->customData->contentCategory = $this->getBreadcrumb($taxon);
    }

    private function getBreadcrumbs(TaxonInterface $taxon): array
    {
        $breadcrumbs = [];

        for ($breadcrumb = $taxon->getParent(); null !== $breadcrumb; $breadcrumb = $breadcrumb->getParent()) {
            array_unshift($breadcrumbs, $breadcrumb);
        }

        return $breadcrumbs;
    }

    private function getBreadcrumb(TaxonInterface $taxon): string
    {
        return implode(' > ', array_map(static function (TaxonInterface $taxon) {
            return $taxon->getName();
        }, $this->getBreadcrumbs($taxon)));
    }
}
