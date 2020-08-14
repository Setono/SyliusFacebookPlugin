<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Builder;

use Webmozart\Assert\Assert;

/**
 * @mixin Builder
 */
trait ContentTypeAwareBuilderTrait
{
    public function setContentType(string $contentType): self
    {
        \assert($this instanceof Builder);

        Assert::oneOf($contentType, [self::CONTENT_TYPE_PRODUCT, self::CONTENT_TYPE_PRODUCT_GROUP]);

        $this->data['content_type'] = $contentType;

        return $this;
    }
}
