<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Repository;

use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

interface PixelRepositoryInterface extends RepositoryInterface
{
    /**
     * Returns the pixels that are enabled and enabled on the given channel
     *
     * @param ChannelInterface $channel
     *
     * @return array
     */
    public function findEnabledByChannel(ChannelInterface $channel): array;
}
