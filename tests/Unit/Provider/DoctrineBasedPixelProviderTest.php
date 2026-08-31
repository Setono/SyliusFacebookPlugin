<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\MetaConversionsApi\Pixel\Pixel as ConversionsApiPixel;
use Setono\SyliusFacebookPlugin\Model\Pixel;
use Setono\SyliusFacebookPlugin\Provider\DoctrineBasedPixelProvider;
use Setono\SyliusFacebookPlugin\Repository\PixelRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\Channel;

final class DoctrineBasedPixelProviderTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_provides_the_enabled_pixels_of_the_current_channel_as_conversions_api_pixels(): void
    {
        $channel = new Channel();

        $pixelWithAccessToken = new Pixel();
        $pixelWithAccessToken->setPixelId('123');
        $pixelWithAccessToken->setAccessToken('access_token');

        $pixelWithoutAccessToken = new Pixel();
        $pixelWithoutAccessToken->setPixelId('456');

        $channelContext = $this->prophesize(ChannelContextInterface::class);
        $channelContext->getChannel()->willReturn($channel);

        $pixelRepository = $this->prophesize(PixelRepositoryInterface::class);
        $pixelRepository->findEnabledByChannel($channel)->willReturn([$pixelWithAccessToken, $pixelWithoutAccessToken]);

        $provider = new DoctrineBasedPixelProvider($pixelRepository->reveal(), $channelContext->reveal());

        self::assertEquals([
            new ConversionsApiPixel('123', 'access_token'),
            new ConversionsApiPixel('456'),
        ], $provider->getPixels());
    }

    /**
     * @test
     */
    public function it_provides_no_pixels_when_none_are_enabled(): void
    {
        $channel = new Channel();

        $channelContext = $this->prophesize(ChannelContextInterface::class);
        $channelContext->getChannel()->willReturn($channel);

        $pixelRepository = $this->prophesize(PixelRepositoryInterface::class);
        $pixelRepository->findEnabledByChannel($channel)->willReturn([]);

        $provider = new DoctrineBasedPixelProvider($pixelRepository->reveal(), $channelContext->reveal());

        self::assertSame([], $provider->getPixels());
    }
}
