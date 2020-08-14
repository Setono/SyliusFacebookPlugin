<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Builder;

final class PurchaseBuilder extends Builder
{
    use ContentIdsAwareBuilderTrait,
        ContentsAwareBuilderTrait,
        ContentTypeAwareBuilderTrait,
        ValueAwareBuilderTrait
    ;
}
