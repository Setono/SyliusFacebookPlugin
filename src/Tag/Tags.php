<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Tag;

final class Tags
{
    public const TAG_LIBRARY = 'setono_sylius_facebook_tracking_library';
    public const TAG_PURCHASE = 'setono_sylius_facebook_tracking_purchase';
    public const TAG_ADD_TO_CART = 'setono_sylius_facebook_tracking_add_to_cart';
    public const TAG_VIEW_CONTENT = 'setono_sylius_facebook_tracking_view_content';

    private function __construct()
    {
    }
}
