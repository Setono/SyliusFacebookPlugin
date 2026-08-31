<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\Menu;

use Knp\Menu\Integration\Symfony\RoutingExtension;
use Knp\Menu\ItemInterface;
use Knp\Menu\MenuFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusFacebookPlugin\Menu\AdminMenuListener;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class AdminMenuListenerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_adds_the_pixel_index_to_the_marketing_menu(): void
    {
        [$factory, $menu] = $this->createMenu();
        $menu->addChild('catalog');
        $menu->addChild('marketing');

        (new AdminMenuListener())->addAdminMenuItems(new MenuBuilderEvent($factory, $menu));

        $marketing = $menu->getChild('marketing');
        self::assertNotNull($marketing);

        $this->assertHasPixelIndexItem($marketing);

        $catalog = $menu->getChild('catalog');
        self::assertNotNull($catalog);
        self::assertNull($catalog->getChild('facebook_tracking'));
    }

    /**
     * @test
     */
    public function it_adds_the_pixel_index_to_the_first_menu_when_there_is_no_marketing_menu(): void
    {
        [$factory, $menu] = $this->createMenu();
        $menu->addChild('catalog');
        $menu->addChild('sales');

        (new AdminMenuListener())->addAdminMenuItems(new MenuBuilderEvent($factory, $menu));

        $catalog = $menu->getChild('catalog');
        self::assertNotNull($catalog);

        $this->assertHasPixelIndexItem($catalog);
    }

    private function assertHasPixelIndexItem(ItemInterface $parent): void
    {
        $item = $parent->getChild('facebook_tracking');
        self::assertNotNull($item);
        self::assertSame('/admin/facebook/pixels/', $item->getUri());
        self::assertSame('setono_sylius_facebook.ui.facebook', $item->getLabel());
        self::assertSame('facebook', $item->getLabelAttribute('icon'));
    }

    /**
     * @return array{MenuFactory, ItemInterface}
     */
    private function createMenu(): array
    {
        $urlGenerator = $this->prophesize(UrlGeneratorInterface::class);
        $urlGenerator
            ->generate('setono_sylius_facebook_admin_pixel_index', [], UrlGeneratorInterface::ABSOLUTE_PATH)
            ->willReturn('/admin/facebook/pixels/')
        ;

        $factory = new MenuFactory();
        $factory->addExtension(new RoutingExtension($urlGenerator->reveal()));

        return [$factory, $factory->createItem('root')];
    }
}
