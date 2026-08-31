<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\EventSubscriber;

use ArrayIterator;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Setono\SyliusFacebookPlugin\Event\CategoryViewedEvent;
use Setono\SyliusFacebookPlugin\EventSubscriber\ViewCategorySubscriber;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Sylius\Component\Taxonomy\Model\TaxonInterface;
use Sylius\Component\Taxonomy\Repository\TaxonRepositoryInterface;
use Symfony\Component\HttpFoundation\Request;

final class ViewCategorySubscriberTest extends EventSubscriberTestCase
{
    /** @var ObjectProphecy<TaxonRepositoryInterface<TaxonInterface>> */
    private ObjectProphecy $taxonRepository;

    protected function setUp(): void
    {
        parent::setUp();

        /** @var ObjectProphecy<TaxonRepositoryInterface<TaxonInterface>> $taxonRepository */
        $taxonRepository = $this->prophesize(TaxonRepositoryInterface::class);
        $this->taxonRepository = $taxonRepository;
    }

    /**
     * @test
     */
    public function it_subscribes_to_the_product_index_event(): void
    {
        self::assertSame(['sylius.product.index' => 'track'], ViewCategorySubscriber::getSubscribedEvents());
    }

    /**
     * @test
     */
    public function it_raises_a_category_viewed_event_with_the_first_products_of_the_taxon(): void
    {
        $this->taxonRepository->findOneBySlug('t-shirts', 'en_US')->willReturn($this->createTaxon());
        $this->eventDispatcher
            ->dispatch(Argument::that(static function (object $event): bool {
                if (!$event instanceof \Setono\MetaConversionsApiBundle\Event\ConversionsApiEventRaised) {
                    return false;
                }

                return $event->event instanceof CategoryViewedEvent && ['T_SHIRT_1', 'T_SHIRT_2'] === $event->event->customData->contentIds;
            }))
            ->shouldBeCalledOnce()
            ->willReturnArgument(0)
        ;

        $this->createSubscriber()->track(new ResourceControllerEvent($this->createGridView('t-shirts', [
            $this->createProductWithCode('T_SHIRT_1'),
            new \stdClass(),
            $this->createProductWithCode(null),
            $this->createProductWithCode('T_SHIRT_2'),
        ])));
    }

    /**
     * @test
     */
    public function it_only_includes_the_first_ten_products(): void
    {
        $this->taxonRepository->findOneBySlug('t-shirts', 'en_US')->willReturn($this->createTaxon());
        $this->eventDispatcher
            ->dispatch(Argument::that(static function (object $event): bool {
                if (!$event instanceof \Setono\MetaConversionsApiBundle\Event\ConversionsApiEventRaised) {
                    return false;
                }

                return $event->event instanceof CategoryViewedEvent && 10 === count($event->event->customData->contentIds) && 'T_SHIRT_10' === end($event->event->customData->contentIds);
            }))
            ->shouldBeCalledOnce()
            ->willReturnArgument(0)
        ;

        $products = [];
        for ($i = 1; $i <= 12; ++$i) {
            $products[] = $this->createProductWithCode(sprintf('T_SHIRT_%d', $i));
        }

        $this->createSubscriber()->track(new ResourceControllerEvent($this->createGridView('t-shirts', $products)));
    }

    /**
     * @test
     */
    public function it_raises_a_category_viewed_event_without_products_when_the_grid_data_is_not_traversable(): void
    {
        $this->taxonRepository->findOneBySlug('t-shirts', 'en_US')->willReturn($this->createTaxon());
        $this->eventDispatcher
            ->dispatch(Argument::that(static fn (object $event): bool => $event instanceof \Setono\MetaConversionsApiBundle\Event\ConversionsApiEventRaised &&
                $event->event instanceof CategoryViewedEvent &&
                [] === $event->event->customData->contentIds))
            ->shouldBeCalledOnce()
            ->willReturnArgument(0)
        ;
        $this->logger->error(Argument::cetera())->shouldNotBeCalled();

        $requestConfiguration = $this->prophesize(RequestConfiguration::class);
        $requestConfiguration->getRequest()->willReturn(new Request([], [], ['slug' => 't-shirts']));

        $gridView = $this->prophesize(ResourceGridView::class);
        $gridView->getRequestConfiguration()->willReturn($requestConfiguration->reveal());
        $gridView->getData()->willReturn(null);

        $this->createSubscriber()->track(new ResourceControllerEvent($gridView->reveal()));
    }

    /**
     * @test
     */
    public function it_does_nothing_when_the_subject_is_not_a_grid_view(): void
    {
        $this->expectNoConversionsApiEvent();

        $this->createSubscriber()->track(new ResourceControllerEvent(new \stdClass()));
    }

    /**
     * @test
     */
    public function it_does_nothing_when_the_request_has_no_taxon_slug(): void
    {
        $this->expectNoConversionsApiEvent();
        $this->taxonRepository->findOneBySlug(Argument::cetera())->shouldNotBeCalled();

        $this->createSubscriber()->track(new ResourceControllerEvent($this->createGridView(null, [])));
    }

    /**
     * @test
     */
    public function it_does_nothing_when_the_taxon_does_not_exist(): void
    {
        $this->expectNoConversionsApiEvent();
        $this->taxonRepository->findOneBySlug('unknown', 'en_US')->willReturn(null);

        $this->createSubscriber()->track(new ResourceControllerEvent($this->createGridView('unknown', [])));
    }

    private function createSubscriber(): ViewCategorySubscriber
    {
        $localeContext = $this->prophesize(LocaleContextInterface::class);
        $localeContext->getLocaleCode()->willReturn('en_US');

        $subscriber = new ViewCategorySubscriber($this->eventDispatcher->reveal(), $localeContext->reveal(), $this->taxonRepository->reveal());
        $subscriber->setLogger($this->logger->reveal());

        return $subscriber;
    }

    /**
     * @param list<object> $data
     */
    private function createGridView(?string $slug, array $data): ResourceGridView
    {
        $requestConfiguration = $this->prophesize(RequestConfiguration::class);
        $requestConfiguration->getRequest()->willReturn(new Request([], [], null === $slug ? [] : ['slug' => $slug]));

        $gridView = $this->prophesize(ResourceGridView::class);
        $gridView->getRequestConfiguration()->willReturn($requestConfiguration->reveal());
        $gridView->getData()->willReturn(new ArrayIterator($data));

        return $gridView->reveal();
    }

    private function createTaxon(): TaxonInterface
    {
        $taxon = $this->prophesize(TaxonInterface::class);
        $taxon->getName()->willReturn('T-Shirts');
        $taxon->getParent()->willReturn(null);

        return $taxon->reveal();
    }

    private function createProductWithCode(?string $code): ProductInterface
    {
        $product = $this->prophesize(ProductInterface::class);
        $product->getCode()->willReturn($code);

        return $product->reveal();
    }
}
