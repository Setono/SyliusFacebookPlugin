<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\DataMapper;

use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use SplPriorityQueue;

final class CompositeDataMapper implements DataMapperInterface
{
    private SplPriorityQueue $dataMappers;

    public function __construct()
    {
        $this->dataMappers = new SplPriorityQueue();
    }

    public function add(DataMapperInterface $dataMapper, int $priority = 0): void
    {
        $this->dataMappers->insert($dataMapper, $priority);
    }

    public function supports($source, ServerSideEventInterface $target, array $context = []): bool
    {
        foreach ($this->getDataMappers() as $dataMapper) {
            if ($dataMapper->supports($source, $target, $context)) {
                return true;
            }
        }

        return false;
    }

    public function map($source, ServerSideEventInterface $target, array $context = []): void
    {
        foreach ($this->getDataMappers() as $dataMapper) {
            if ($dataMapper->supports($source, $target, $context)) {
                $dataMapper->map($source, $target, $context);
            }
        }
    }

    /**
     * @return iterable<array-key, DataMapperInterface>
     * @psalm-suppress MixedReturnTypeCoercion
     */
    private function getDataMappers(): iterable
    {
        // @todo use another implementation of a priority queue which does not dequeue on foreach
        return clone $this->dataMappers;
    }
}
