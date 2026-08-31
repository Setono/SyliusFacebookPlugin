<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Context;

use Setono\SyliusFacebookPlugin\Model\PixelInterface;
use Setono\SyliusFacebookPlugin\Repository\PixelRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;

final class PixelContext implements PixelContextInterface
{
    /**
     * Caches pixels
     *
     * @var array<array-key, PixelInterface>|null
     */
    private ?array $pixels = null;

    public function __construct(
        private readonly ChannelContextInterface $channelContext,
        private readonly PixelRepositoryInterface $pixelRepository,
    ) {
    }

    public function getPixels(): array
    {
        $this->pixels ??= $this->pixelRepository->findEnabledByChannel($this->channelContext->getChannel());

        return $this->pixels;
    }

    public function hasPixels(): bool
    {
        return count($this->getPixels()) > 0;
    }
}
