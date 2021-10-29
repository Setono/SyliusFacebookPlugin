<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\DataMapper;

use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;

interface DataMapperInterface
{
    /**
     * Returns true if this data mapper supports the given $source and $target
     *
     * @param array<string, mixed> $context
     */
    public function supports($source, ServerSideEventInterface $target, array $context = []): bool;

    /**
     * Maps $source to $target. This means properties on $target
     * are updated, but properties on $source remain untouched
     *
     * @param array<string, mixed> $context
     */
    public function map($source, ServerSideEventInterface $target, array $context = []): void;
}
