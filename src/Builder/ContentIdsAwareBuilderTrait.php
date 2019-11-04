<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Builder;

use Webmozart\Assert\Assert;

trait ContentIdsAwareBuilderTrait
{
    /** @var array */
    protected $data = [];

    /**
     * @param mixed $contentId
     */
    public function addContentId($contentId): self
    {
        if (!isset($this->data['content_ids'])) {
            $this->data['content_ids'] = [];
        }

        Assert::scalar($contentId);

        $this->data['content_ids'][] = $contentId;

        return $this;
    }
}
