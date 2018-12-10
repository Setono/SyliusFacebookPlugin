<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Menu;

use Knp\Menu\ItemInterface;
use Setono\SyliusFacebookTrackingPlugin\Context\FacebookConfigContextInterface;
use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class FacebookTrackingMenuBuilder
{
    /** @var FacebookConfigContextInterface */
    private $facebookConfigContext;

    public function __construct(FacebookConfigContextInterface $facebookConfigContext)
    {
        $this->facebookConfigContext = $facebookConfigContext;
    }

    public function addFacebookTrackingItem(MenuBuilderEvent $event): void
    {
        /** @var ItemInterface $catalogMenu */
        $catalogMenu = $event->getMenu()->getChild('catalog');

        $catalogMenu
            ->addChild('facebook_tracking', [
                'route' => 'setono_sylius_facebook_tracking_plugin_admin_facebook_config_update',
                'routeParameters' => ['id' => $this->facebookConfigContext->getConfig()->getId()],
            ])
            ->setLabel('setono_sylius_facebook_tracking_plugin.ui.facebook_config_index')
            ->setLabelAttribute('icon', 'bullhorn')
        ;
    }
}
