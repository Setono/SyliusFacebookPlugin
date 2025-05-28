<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Provider;

use Setono\MetaConversionsApi\Pixel\Pixel;
use Setono\MetaConversionsApiBundle\Provider\PixelProviderInterface;
use Setono\SyliusFacebookPlugin\Repository\PixelRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;

final class DoctrineBasedPixelProvider implements PixelProviderInterface
{
    public function __construct(
        private readonly PixelRepositoryInterface $pixelRepository,
        private readonly ChannelContextInterface $channelContext,
    ) {
    }

    public function getPixels(): array
    {
        $pixelEntities = $this->pixelRepository->findEnabledByChannel($this->channelContext->getChannel());

        $pixels = [];

        foreach ($pixelEntities as $pixelEntity) {
            $pixels[] = new Pixel((string) $pixelEntity->getPixelId(), $pixelEntity->getAccessToken());
        }

        return $pixels;
    }
}
