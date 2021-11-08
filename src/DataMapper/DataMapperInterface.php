<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\DataMapper;

use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;

interface DataMapperInterface
{
    /**
     * Returns true if this data mapper supports the given $source and $target
     */
    public function supports(object $source, ServerSideEventInterface $target, array $context = []): bool;

    /**
     * Maps $source to $target. This means properties on $target
     * are updated, but properties on $source remain untouched
     */
    public function map(object $source, ServerSideEventInterface $target, array $context = []): void;
}
