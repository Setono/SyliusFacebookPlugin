<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\DataMapper;

use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;

/* not final */ class ViewCategoryDataMapper implements DataMapperInterface
{
    protected LocaleContextInterface $localeContext;

    protected TaxonRepositoryInterface $taxonRepository;

    public function __construct(
        LocaleContextInterface $localeContext,
        TaxonRepositoryInterface $taxonRepository
    ) {
        $this->localeContext = $localeContext;
        $this->taxonRepository = $taxonRepository;
    }

    /**
     * @psalm-assert-if-true ResourceGridView $source
     */
    public function supports($source, ServerSideEventInterface $target, array $context = []): bool
    {
        return $source instanceof ResourceGridView;
    }

    /**
     * @param ResourceGridView $source
     */
    public function map($source, ServerSideEventInterface $target, array $context = []): void
    {
        $request = $source->getRequestConfiguration()->getRequest();

        $customData = $target->getCustomData();
        $customData
            ->setContentType(ServerSideEventInterface::CONTENT_TYPE_PRODUCT)
            ->setContentIds($this->getContentIds($source))
        ;

        /** @var string|null $slug */
        $slug = $request->attributes->get('slug');
        if (null === $slug) {
            return;
        }

        $locale = $this->localeContext->getLocaleCode();
        $taxon = $this->taxonRepository->findOneBySlug($slug, $locale);
        if (null === $taxon) {
            return;
        }

        $customData
            ->setContentName($taxon->getName())
            ->setContentCategory($this->getContentCategory($taxon))
        ;
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

    protected function getContentIds(ResourceGridView $gridView, int $max = 10): array
    {
        return array_slice(array_map(function (ProductInterface $product) {
            return $product->getCode();
        }, $gridView->getData()), 0, $max);
    }
}
