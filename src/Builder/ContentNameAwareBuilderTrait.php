<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Builder;

trait ContentNameAwareBuilderTrait
{
    /**
     * @var array
     */
    protected $data = [];

    public function setContentName(string $contentName): self
    {
        $this->data['content_name'] = $contentName;

        return $this;
    }
}
