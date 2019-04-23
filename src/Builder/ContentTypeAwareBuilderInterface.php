<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Builder;

use InvalidArgumentException;

interface ContentTypeAwareBuilderInterface
{
    /**
     * @param string $contentType
     *
     * @return BuilderInterface
     *
     * @throws InvalidArgumentException if $contentType is not valid
     */
    public function setContentType(string $contentType): BuilderInterface;
}
