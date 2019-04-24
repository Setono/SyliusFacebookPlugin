<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Builder;

use Webmozart\Assert\Assert;

trait ContentTypeAwareBuilderTrait
{
    /**
     * @var array
     */
    protected $data = [];

    public function setContentType(string $contentType): self
    {
        Assert::oneOf($contentType, ['product', 'product_group']);

        $this->data['content_type'] = $contentType;

        return $this;
    }
}
