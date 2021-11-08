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

    private ChannelContextInterface $channelContext;

    private PixelRepositoryInterface $pixelRepository;

    public function __construct(
        ChannelContextInterface $channelContext,
        PixelRepositoryInterface $pixelRepository
    ) {
        $this->channelContext = $channelContext;
        $this->pixelRepository = $pixelRepository;
    }

    public function getPixels(): array
    {
        if (null === $this->pixels) {
            $this->pixels = $this->pixelRepository->findEnabledByChannel($this->channelContext->getChannel());
        }

        return $this->pixels;
    }

    public function hasPixels(): bool
    {
        return count($this->getPixels()) > 0;
    }
}
