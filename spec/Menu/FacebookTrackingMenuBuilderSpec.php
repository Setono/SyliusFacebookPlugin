<?php

declare(strict_types=1);

namespace spec\Setono\SyliusFacebookTrackingPlugin\Menu;

use Knp\Menu\ItemInterface;
use PhpSpec\ObjectBehavior;
use Setono\SyliusFacebookTrackingPlugin\Context\FacebookConfigContextInterface;
use Setono\SyliusFacebookTrackingPlugin\Model\FacebookConfigInterface;
use Setono\SyliusFacebookTrackingPlugin\Menu\FacebookTrackingMenuBuilder;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

class FacebookTrackingMenuBuilderSpec extends ObjectBehavior
{
    function let(FacebookConfigContextInterface $facebookConfigContext): void
    {
        $this->beConstructedWith($facebookConfigContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FacebookTrackingMenuBuilder::class);
    }

    function it_adds_facebook_pixelcode_menu(
        MenuBuilderEvent $menuBuilderEvent,
        ItemInterface $menu,
        ItemInterface $catalogMenu,
        ItemInterface $facebookMenuItem,
        FacebookConfigContextInterface $facebookConfigContext,
        FacebookConfigInterface $facebookConfig
    ): void {
        $facebookConfig->getId()->willReturn(1);
        $facebookConfigContext->getConfig()->willReturn($facebookConfig);
        $menuBuilderEvent->getMenu()->willReturn($menu);
        $menu->getChild('catalog')->willReturn($catalogMenu);
        $catalogMenu->addChild('facebook_tracking', [
            'route' => 'setono_sylius_facebook_tracking_plugin_admin_facebook_config_update',
            'routeParameters' => ['id' => 1],
        ])->willReturn($facebookMenuItem);

        $facebookMenuItem->setLabel('setono_sylius_facebook_tracking_plugin.ui.facebook_config_index')->willReturn($facebookMenuItem);
        $facebookMenuItem->setLabelAttribute('icon', 'bullhorn')->shouldBeCalled();

        $this->addFacebookTrackingItem($menuBuilderEvent);
    }
}
