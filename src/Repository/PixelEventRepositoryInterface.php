<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Repository;

use Setono\SyliusFacebookPlugin\Model\PixelEventInterface;
use Setono\SyliusFacebookPlugin\Model\PixelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface PixelEventRepositoryInterface extends RepositoryInterface
{
    public function getCountByPixelAndState(PixelInterface $pixel, string $state): int;

    /**
     * Returns true if there are pending consented hits created before $delay seconds ago
     *
     * @param int $delay in seconds
     */
    public function hasConsentedPending(int $delay = 0): bool;

    /**
     * Will assign the given bulk identifier to pending consented hits
     *
     * @param int $delay in seconds
     * @param int $limit maximum number of rows to update
     */
    public function assignBulkIdentifierToPendingConsented(string $bulkIdentifier, int $delay = 0, int $limit = 1000): void;

    /**
     * @return array<array-key, PixelEventInterface>
     */
    public function findByBulkIdentifier(string $bulkIdentifier): array;

    public function resetFailedByPixel(PixelInterface $pixel): void;

    public function removeSent(int $delay = 0): int;
}
