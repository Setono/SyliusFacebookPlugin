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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

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
        $pixelRepository->findEnabled()->shouldNotBeCalled();

        $provider = new DoctrineBasedPixelProvider($pixelRepository->reveal(), $channelContext->reveal(), self::requestStackWithRequest());

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

        $provider = new DoctrineBasedPixelProvider($pixelRepository->reveal(), $channelContext->reveal(), self::requestStackWithRequest());

        self::assertSame([], $provider->getPixels());
    }

    /**
     * The bundle asks for the pixels again when an event is sent, to add back the access tokens.
     * In a Messenger worker there is no request, and asking Sylius for the channel would throw
     *
     * @test
     */
    public function it_provides_every_enabled_pixel_when_there_is_no_request(): void
    {
        $pixel = new Pixel();
        $pixel->setPixelId('123');
        $pixel->setAccessToken('access_token');

        $channelContext = $this->prophesize(ChannelContextInterface::class);
        $channelContext->getChannel()->shouldNotBeCalled();

        $pixelRepository = $this->prophesize(PixelRepositoryInterface::class);
        $pixelRepository->findEnabled()->willReturn([$pixel]);

        $provider = new DoctrineBasedPixelProvider($pixelRepository->reveal(), $channelContext->reveal(), new RequestStack());

        self::assertEquals([new ConversionsApiPixel('123', 'access_token')], $provider->getPixels());
    }

    private static function requestStackWithRequest(): RequestStack
    {
        $requestStack = new RequestStack();
        $requestStack->push(Request::create('https://example.com'));

        return $requestStack;
    }
}
