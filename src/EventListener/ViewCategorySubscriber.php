<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventListener;

use Setono\BotDetectionBundle\BotDetector\BotDetectorInterface;
use Setono\SyliusFacebookPlugin\Context\PixelContextInterface;
use Setono\SyliusFacebookPlugin\Data\ViewCategoryData;
use Setono\SyliusFacebookPlugin\Generator\PixelEventsGeneratorInterface;
use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\RequestStack;
use Webmozart\Assert\Assert;

/**
 * See https://developers.facebook.com/docs/marketing-api/audiences/guides/dynamic-product-audiences/#setuppixel
 * for reference of the 'ViewCategory' custom event
 */
final class ViewCategorySubscriber extends AbstractSubscriber
{
    protected LocaleContextInterface $localeContext;

    protected TaxonRepositoryInterface $taxonRepository;

    public function __construct(
        RequestStack $requestStack,
        FirewallMap $firewallMap,
        PixelContextInterface $pixelContext,
        PixelEventsGeneratorInterface $pixelEventsGenerator,
        BotDetectorInterface $botDetector,
        LocaleContextInterface $localeContext,
        TaxonRepositoryInterface $taxonRepository
    ) {
        parent::__construct(
            $requestStack,
            $firewallMap,
            $pixelContext,
            $pixelEventsGenerator,
            $botDetector
        );

        $this->localeContext = $localeContext;
        $this->taxonRepository = $taxonRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.product.index' => [
                'trackCustom',
            ],
        ];
    }

    public function trackCustom(ResourceControllerEvent $event): void
    {
        if (!$this->isRequestEligible() || !$this->pixelContext->hasPixels()) {
            return;
        }

        $gridView = $event->getSubject();
        if (!$gridView instanceof ResourceGridView) {
            return;
        }

        $viewCategoryData = new ViewCategoryData(
            $this->getProducts($gridView),
            $this->getTaxon($gridView),
        );

        $this->pixelEventsGenerator->generatePixelEvents(
            $viewCategoryData,
            ServerSideEventInterface::CUSTOM_EVENT_VIEW_CATEGORY
        );
    }

    /**
     * @return ProductInterface[]
     */
    protected function getProducts(ResourceGridView $gridView): array
    {
        $data = $gridView->getData();
        Assert::isInstanceOf($data, \Traversable::class);

        $result = iterator_to_array($data);
        Assert::allIsInstanceOf($result, ProductInterface::class);

        return $result;
    }

    protected function getTaxon(ResourceGridView $gridView): ?TaxonInterface
    {
        $request = $gridView->getRequestConfiguration()->getRequest();

        /** @var string|null $slug */
        $slug = $request->attributes->get('slug');
        if (null === $slug) {
            return null;
        }

        $locale = $this->localeContext->getLocaleCode();

        return $this->taxonRepository->findOneBySlug($slug, $locale);
    }
}
