<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Provider;

use Setono\MetaConversionsApi\Pixel\Pixel;
use Setono\MetaConversionsApiBundle\Provider\PixelProviderInterface;
use Setono\SyliusFacebookPlugin\Model\PixelInterface;
use Setono\SyliusFacebookPlugin\Repository\PixelRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class DoctrineBasedPixelProvider implements PixelProviderInterface
{
    public function __construct(
        private readonly PixelRepositoryInterface $pixelRepository,
        private readonly ChannelContextInterface $channelContext,
        private readonly RequestStack $requestStack,
    ) {
    }

    public function getPixels(): array
    {
        $pixels = [];

        foreach ($this->getPixelEntities() as $pixelEntity) {
            $pixels[] = new Pixel((string) $pixelEntity->getPixelId(), $pixelEntity->getAccessToken());
        }

        return $pixels;
    }

    /**
     * While a request is handled, the pixels are the enabled pixels of the current channel.
     *
     * The bundle also calls this provider when an event is sent, to add back the access tokens that never travel with
     * the queued event. That may happen in a Messenger worker where there is no request, and hence no channel. The
     * access tokens are matched by pixel id against the pixels the event was raised for, so returning every enabled
     * pixel is safe there.
     *
     * It also means that an event your application raises outside of a request, in a console command for instance,
     * goes to every enabled pixel, because there is no channel to narrow it down by
     *
     * @return array<array-key, PixelInterface>
     */
    private function getPixelEntities(): array
    {
        if (null === $this->requestStack->getMainRequest()) {
            return $this->pixelRepository->findEnabled();
        }

        return $this->pixelRepository->findEnabledByChannel($this->channelContext->getChannel());
    }
}
