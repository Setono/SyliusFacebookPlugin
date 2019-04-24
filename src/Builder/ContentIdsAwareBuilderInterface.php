<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Builder;

use InvalidArgumentException;

interface ContentIdsAwareBuilderInterface
{
    /**
     * @param int|string $contentId
     *
     * @return BuilderInterface
     *
     * @throws InvalidArgumentException if $contentId is not a scalar
     */
    public function addContentId($contentId): BuilderInterface;
}
