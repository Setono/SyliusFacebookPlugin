<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\Event;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusFacebookPlugin\Event\CategoryViewedEvent;
use Sylius\Component\Taxonomy\Model\TaxonInterface;

final class CategoryViewedEventTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_populates_custom_data_from_the_taxon_and_the_products(): void
    {
        $men = $this->createTaxon('Men', null);
        $clothes = $this->createTaxon('Clothes', $men);
        $tShirts = $this->createTaxon('T-Shirts', $clothes);

        $event = new CategoryViewedEvent($tShirts, ['T_SHIRT_1', 'T_SHIRT_2']);

        self::assertSame('ViewCategory', $event->eventName);
        self::assertSame('product', $event->customData->contentType);
        self::assertSame('T-Shirts', $event->customData->contentName);
        self::assertSame(['T_SHIRT_1', 'T_SHIRT_2'], $event->customData->contentIds);
        self::assertSame('Men > Clothes', $event->customData->contentCategory);
    }

    /**
     * @test
     */
    public function it_has_an_empty_content_category_for_a_root_taxon(): void
    {
        $event = new CategoryViewedEvent($this->createTaxon('Men', null));

        self::assertSame('', $event->customData->contentCategory);
        self::assertSame([], $event->customData->contentIds);
    }

    private function createTaxon(string $name, ?TaxonInterface $parent): TaxonInterface
    {
        $taxon = $this->prophesize(TaxonInterface::class);
        $taxon->getName()->willReturn($name);
        $taxon->getParent()->willReturn($parent);

        return $taxon->reveal();
    }
}
