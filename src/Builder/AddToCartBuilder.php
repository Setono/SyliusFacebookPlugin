<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Builder;

final class AddToCartBuilder extends Builder
{
    use ContentIdsAwareBuilderTrait,
        ContentsAwareBuilderTrait,
        ContentTypeAwareBuilderTrait,
        ValueAwareBuilderTrait
    ;

    public const EVENT_NAME = 'setono_sylius_facebook_tracking.builder.add_to_cart';
}
