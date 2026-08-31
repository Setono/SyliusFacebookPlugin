<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\Event;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\MetaConversionsApi\Event\Event;
use Setono\SyliusFacebookPlugin\Event\ProductViewedEvent;
use Sylius\Component\Core\Model\ProductInterface;
use Sylius\Component\Core\Model\TaxonInterface;

final class ProductViewedEventTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_populates_custom_data_from_the_product_and_its_main_taxon(): void
    {
        $event = new ProductViewedEvent($this->createProduct($this->createTaxon('Jeans')));

        self::assertSame(Event::EVENT_VIEW_CONTENT, $event->eventName);
        self::assertSame('product', $event->customData->contentType);
        self::assertSame('Office grey jeans', $event->customData->contentName);
        self::assertSame(['OFFICE_GREY_JEANS'], $event->customData->contentIds);
        self::assertSame('Jeans', $event->customData->contentCategory);
    }

    /**
     * @test
     */
    public function it_falls_back_to_the_first_taxon_when_the_product_has_no_main_taxon(): void
    {
        $event = new ProductViewedEvent($this->createProduct(null, [$this->createTaxon('Clothes'), $this->createTaxon('Jeans')]));

        self::assertSame('Clothes', $event->customData->contentCategory);
    }

    /**
     * @test
     */
    public function it_has_no_content_category_when_the_product_has_no_taxons(): void
    {
        $event = new ProductViewedEvent($this->createProduct(null));

        self::assertNull($event->customData->contentCategory);
    }

    /**
     * @param list<TaxonInterface> $taxons
     */
    private function createProduct(?TaxonInterface $mainTaxon, array $taxons = []): ProductInterface
    {
        $product = $this->prophesize(ProductInterface::class);
        $product->getName()->willReturn('Office grey jeans');
        $product->getCode()->willReturn('OFFICE_GREY_JEANS');
        $product->getMainTaxon()->willReturn($mainTaxon);
        $product->getTaxons()->willReturn(new ArrayCollection($taxons));

        return $product->reveal();
    }

    private function createTaxon(string $name): TaxonInterface
    {
        $taxon = $this->prophesize(TaxonInterface::class);
        $taxon->getName()->willReturn($name);

        return $taxon->reveal();
    }
}
