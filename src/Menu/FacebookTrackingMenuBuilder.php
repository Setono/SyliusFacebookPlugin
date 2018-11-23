<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Menu;

use Sylius\Bundle\UiBundle\Menu\Event\MenuBuilderEvent;

final class FacebookTrackingMenuBuilder
{
    public function addFacebookTrackingItem(MenuBuilderEvent $event): void
    {
        $catalogMenu = $event->getMenu()->getChild('catalog');

        $catalogMenu
            ->addChild('facebook_tracking',
                ['route' => 'setono_sylius_facebook_tracking_plugin_admin_facebook_config_index'])
            ->setLabel('setono_sylius_facebook_tracking_plugin.ui.facebook_config_index')
            ->setLabelAttribute('icon', 'bullhorn')
        ;
    }
}
