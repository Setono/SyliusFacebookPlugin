<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Repository;

use Setono\SyliusFacebookPlugin\Model\PixelEventInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface PixelEventRepositoryInterface extends RepositoryInterface
{
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

    public function removeSent(int $delay = 0): int;
}
