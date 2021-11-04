<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\DataMapper;

use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use SplPriorityQueue;
use Webmozart\Assert\Assert;

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

    public function supports(object $source, ServerSideEventInterface $target, array $context = []): bool
    {
        foreach ($this->getDataMappers() as $dataMapper) {
            if ($dataMapper->supports($source, $target, $context)) {
                return true;
            }
        }

        return false;
    }

    public function map(object $source, ServerSideEventInterface $target, array $context = []): void
    {
        foreach ($this->getDataMappers() as $dataMapper) {
            if ($dataMapper->supports($source, $target, $context)) {
                $dataMapper->map($source, $target, $context);
            }
        }
    }

    /**
     * @return array<array-key, DataMapperInterface>
     */
    private function getDataMappers(): array
    {
        $dataMappers = iterator_to_array(clone $this->dataMappers);
        Assert::allIsInstanceOf($dataMappers, DataMapperInterface::class);

        return $dataMappers;
    }
}
