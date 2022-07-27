<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventSubscriber;

use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusFacebookPlugin\Event\CategoryViewedEvent;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Traversable;

/**
 * See https://developers.facebook.com/docs/marketing-api/audiences/guides/dynamic-product-audiences/#setuppixel
 * for reference of the 'ViewCategory' custom event
 */
final class ViewCategorySubscriber extends EventSubscriber
{
    private LocaleContextInterface $localeContext;

    private TaxonRepositoryInterface $taxonRepository;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        LocaleContextInterface $localeContext,
        TaxonRepositoryInterface $taxonRepository
    ) {
        parent::__construct($eventDispatcher);

        $this->localeContext = $localeContext;
        $this->taxonRepository = $taxonRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.product.index' => 'track',
        ];
    }

    protected function callback(): callable
    {
        return function (ResourceControllerEvent $event): ?CategoryViewedEvent {
            $gridView = $event->getSubject();
            if (!$gridView instanceof ResourceGridView) {
                return null;
            }

            $taxon = $this->getTaxon($gridView);
            if (null === $taxon) {
                return null;
            }

            return new CategoryViewedEvent($taxon, $this->getProducts($gridView));
        };
    }

    /**
     * @return list<string>
     */
    private function getProducts(ResourceGridView $gridView): array
    {
        $data = $gridView->getData();
        if (!$data instanceof Traversable) {
            return [];
        }

        $codes = [];

        $i = 0;
        $max = 10;

        /** @var mixed $datum */
        foreach ($data as $datum) {
            if ($i >= $max) {
                break;
            }

            if ($datum instanceof ProductInterface) {
                $code = $datum->getCode();
                if (null !== $code) {
                    $codes[] = $code;
                }
            }

            ++$i;
        }

        return $codes;
    }

    private function getTaxon(ResourceGridView $gridView): ?TaxonInterface
    {
        $request = $gridView->getRequestConfiguration()->getRequest();

        $slug = $request->attributes->get('slug');
        if (!is_string($slug)) {
            return null;
        }

        $locale = $this->localeContext->getLocaleCode();

        return $this->taxonRepository->findOneBySlug($slug, $locale);
    }
}
