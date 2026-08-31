<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\Context;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusFacebookPlugin\Context\PixelContext;
use Setono\SyliusFacebookPlugin\Model\Pixel;
use Setono\SyliusFacebookPlugin\Repository\PixelRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\Channel;

final class PixelContextTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_returns_the_enabled_pixels_for_the_current_channel_and_caches_them(): void
    {
        $channel = new Channel();
        $pixel = new Pixel();

        $channelContext = $this->prophesize(ChannelContextInterface::class);
        $channelContext->getChannel()->willReturn($channel);

        $pixelRepository = $this->prophesize(PixelRepositoryInterface::class);
        $pixelRepository->findEnabledByChannel($channel)->willReturn([$pixel])->shouldBeCalledOnce();

        $pixelContext = new PixelContext($channelContext->reveal(), $pixelRepository->reveal());

        self::assertSame([$pixel], $pixelContext->getPixels());
        self::assertSame([$pixel], $pixelContext->getPixels());
        self::assertTrue($pixelContext->hasPixels());
    }

    /**
     * @test
     */
    public function it_has_no_pixels_when_none_are_enabled_for_the_current_channel(): void
    {
        $channel = new Channel();

        $channelContext = $this->prophesize(ChannelContextInterface::class);
        $channelContext->getChannel()->willReturn($channel);

        $pixelRepository = $this->prophesize(PixelRepositoryInterface::class);
        $pixelRepository->findEnabledByChannel($channel)->willReturn([]);

        $pixelContext = new PixelContext($channelContext->reveal(), $pixelRepository->reveal());

        self::assertSame([], $pixelContext->getPixels());
        self::assertFalse($pixelContext->hasPixels());
    }
}
