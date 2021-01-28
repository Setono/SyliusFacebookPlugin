<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventListener;

use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusFacebookPlugin\Builder\ViewCategoryBuilder;
use Setono\SyliusFacebookPlugin\Builder\ViewContentBuilder;
use Setono\SyliusFacebookPlugin\Context\PixelContextInterface;
use Setono\SyliusFacebookPlugin\Event\BuilderEvent;
use Setono\SyliusFacebookPlugin\Tag\FbqTag;
use Setono\SyliusFacebookPlugin\Tag\Tags;
use Setono\TagBag\Tag\TagInterface;
use Setono\TagBag\TagBagInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * See https://developers.facebook.com/docs/marketing-api/audiences/guides/dynamic-product-audiences/#setuppixel
 * for reference of the 'ViewCategory' custom event
 */
final class ViewCategorySubscriber extends TagSubscriber
{
    private TaxonRepositoryInterface $taxonRepository;

    private LocaleContextInterface $localeContext;

    public function __construct(
        TagBagInterface $tagBag,
        PixelContextInterface $pixelContext,
        EventDispatcherInterface $eventDispatcher,
        RequestStack $requestStack,
        FirewallMap $firewallMap,
        TaxonRepositoryInterface $taxonRepository,
        LocaleContextInterface $localeContext
    ) {
        parent::__construct($tagBag, $pixelContext, $eventDispatcher, $requestStack, $firewallMap);

        $this->taxonRepository = $taxonRepository;
        $this->localeContext = $localeContext;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.product.index' => [
                'track',
            ],
        ];
    }

    public function track(ResourceControllerEvent $event): void
    {
        if (!$this->isShopContext() || !$this->hasPixels()) {
            return;
        }

        $gridView = $event->getSubject();
        if (!$gridView instanceof ResourceGridView) {
            return;
        }

        $builder = ViewCategoryBuilder::create()
            ->setContentType(ViewContentBuilder::CONTENT_TYPE_PRODUCT)
        ;

        /** @var string|null $slug */
        $slug = $gridView->getRequestConfiguration()->getRequest()->attributes->get('slug');
        if (null !== $slug) {
            $taxon = $this->taxonRepository->findOneBySlug($slug, $this->localeContext->getLocaleCode());

            if (null !== $taxon) {
                $builder->setContentName($taxon->getName());
                $builder->setContentCategory($this->getContentCategory($taxon));
            }
        }

        $contentIds = $this->getContentIds($gridView);
        foreach ($contentIds as $contentId) {
            $builder->addContentId($contentId);
        }

        $this->eventDispatcher->dispatch(new BuilderEvent($builder, $gridView));

        $this->tagBag->addTag(
            (new FbqTag('ViewCategory', $builder, 'trackCustom'))
                ->setSection(TagInterface::SECTION_BODY_END)
                ->setName(Tags::TAG_VIEW_CATEGORY)
        );
    }

    private function getContentCategory(TaxonInterface $taxon): string
    {
        $contentCategory = '';

        $ancestors = array_reverse($taxon->getAncestors()->toArray());

        foreach ($ancestors as $ancestor) {
            $contentCategory .= $ancestor->getName() . ' > ';
        }

        return rtrim($contentCategory, ' >');
    }

    private function getContentIds(ResourceGridView $gridView, int $max = 10): array
    {
        $ids = [];

        /** @var ProductInterface $product */
        foreach ($gridView->getData() as $idx => $product) {
            if ($idx >= $max) {
                break;
            }

            $ids[] = $product->getCode();
        }

        return $ids;
    }
}
