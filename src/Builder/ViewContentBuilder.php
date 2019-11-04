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
}
