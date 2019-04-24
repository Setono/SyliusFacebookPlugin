<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Builder;

final class ViewContentBuilder extends Builder
{
    use ContentIdsAwareBuilderTrait,
        ContentNameAwareBuilderTrait,
        ContentsAwareBuilderTrait,
        ContentTypeAwareBuilderTrait,
        ValueAwareBuilderTrait
    ;

    public const EVENT_NAME = 'setono_sylius_facebook_tracking.builder.view_content';
}
