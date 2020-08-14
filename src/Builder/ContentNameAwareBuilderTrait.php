<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Builder;

use function assert;

/**
 * @mixin Builder
 */
trait ContentNameAwareBuilderTrait
{
    public function setContentName(string $contentName): self
    {
        assert($this instanceof Builder);

        $this->data['content_name'] = $contentName;

        return $this;
    }
}
