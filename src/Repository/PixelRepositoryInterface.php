<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Repository;

use Setono\SyliusFacebookPlugin\Model\PixelInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface PixelRepositoryInterface extends RepositoryInterface
{
    /**
     * Returns the pixels that are enabled and enabled on the given channel
     *
     * @return array<array-key, PixelInterface>
     */
    public function findEnabledByChannel(ChannelInterface $channel): array;
}
