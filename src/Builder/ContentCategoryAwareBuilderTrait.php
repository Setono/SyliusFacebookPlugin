<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Builder;

/**
 * @mixin Builder
 */
trait ContentCategoryAwareBuilderTrait
{
    public function setContentCategory(string $contentCategory): self
    {
        \assert($this instanceof Builder);

        $this->data['content_category'] = $contentCategory;

        return $this;
    }
}
