<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Builder;

final class AddToCartBuilder extends Builder
{
    use ContentIdsAwareBuilderTrait,
        ContentsAwareBuilderTrait,
        ContentTypeAwareBuilderTrait,
        ValueAwareBuilderTrait
    ;
}
