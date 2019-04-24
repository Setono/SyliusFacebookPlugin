<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Builder;

use InvalidArgumentException;

interface ContentsAwareBuilderInterface
{
    /**
     * @param array|BuilderInterface $content
     *
     * @return BuilderInterface
     *
     * @throws InvalidArgumentException if $content is not the correct type
     */
    public function addContent($content): BuilderInterface;
}
