<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Context;

use Setono\SyliusFacebookTrackingPlugin\Repository\PixelRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;

final class PixelContext implements PixelContextInterface
{
    /**
     * @var ChannelContextInterface
     */
    private $channelContext;

    /**
     * @var PixelRepositoryInterface
     */
    private $propertyRepository;

    public function __construct(ChannelContextInterface $channelContext, PixelRepositoryInterface $propertyRepository)
    {
        $this->channelContext = $channelContext;
        $this->propertyRepository = $propertyRepository;
    }

    public function getPixels(): array
    {
        return $this->propertyRepository->findEnabledByChannel($this->channelContext->getChannel());
    }
}
