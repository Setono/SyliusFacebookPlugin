<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\DataMapper;

use Setono\SyliusFacebookPlugin\Data\ViewCategoryData;
use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Webmozart\Assert\Assert;

/* not final */ class ViewCategoryDataMapper implements DataMapperInterface
{
    /**
     * @psalm-assert-if-true ViewCategoryData $source
     */
    public function supports(object $source, ServerSideEventInterface $target, array $context = []): bool
    {
        return $source instanceof ViewCategoryData;
    }

    public function map(object $source, ServerSideEventInterface $target, array $context = []): void
    {
        Assert::true($this->supports($source, $target, $context));

        $customData = $target->getCustomData();
        $customData
            ->setContentType(ServerSideEventInterface::CONTENT_TYPE_PRODUCT)
            ->setContentIds($this->getContentIds($source->getProducts()))
        ;

        $taxon = $source->getTaxon();
        if (null === $taxon) {
            return;
        }

        /** @psalm-suppress PossiblyNullArgument */
        $customData->setContentName($taxon->getName());
        $customData->setContentCategory($this->getContentCategory($taxon));
    }

    protected function getBreadcrumbs(TaxonInterface $taxon): array
    {
        $breadcrumbs = [];

        array_unshift($breadcrumbs, $taxon);
        for ($breadcrumb = $taxon->getParent(); null !== $breadcrumb; $breadcrumb = $breadcrumb->getParent()) {
            array_unshift($breadcrumbs, $breadcrumb);
        }

        return $breadcrumbs;
    }

    protected function getContentCategory(TaxonInterface $taxon): string
    {
        return implode(' > ', array_map(function (TaxonInterface $taxon) {
            return $taxon->getName();
        }, $this->getBreadcrumbs($taxon)));
    }

    /**
     * @param ProductInterface[] $products
     * @psalm-return array<array-key, string>
     */
    protected function getContentIds(array $products, int $max = 10): array
    {
        return array_slice(array_filter(array_map(function (ProductInterface $product): string {
            return $product->getCode() ?? '';
        }, $products)), 0, $max);
    }
}
