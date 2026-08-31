<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\Fixture\Factory;

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Setono\SyliusFacebookPlugin\Fixture\Factory\PixelExampleFactory;
use Setono\SyliusFacebookPlugin\Model\Pixel;
use Setono\SyliusFacebookPlugin\Model\PixelInterface;
use Sylius\Component\Channel\Model\Channel;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\OptionsResolver\Exception\InvalidOptionsException;

final class PixelExampleFactoryTest extends TestCase
{
    use ProphecyTrait;

    private Channel $webChannel;

    private Channel $mobileChannel;

    private PixelExampleFactory $factory;

    protected function setUp(): void
    {
        $this->webChannel = new Channel();
        $this->webChannel->setCode('WEB');

        $this->mobileChannel = new Channel();
        $this->mobileChannel->setCode('MOBILE');

        /** @var ObjectProphecy<FactoryInterface<PixelInterface>> $pixelFactory */
        $pixelFactory = $this->prophesize(FactoryInterface::class);
        $pixelFactory->createNew()->will(static fn (): Pixel => new Pixel());

        /** @var ObjectProphecy<ChannelRepositoryInterface<ChannelInterface>> $channelRepository */
        $channelRepository = $this->prophesize(ChannelRepositoryInterface::class);
        $channelRepository->findAll()->willReturn([$this->webChannel, $this->mobileChannel]);
        $channelRepository->findOneBy(['code' => 'WEB'])->willReturn($this->webChannel);
        $channelRepository->findOneBy(['code' => 'MOBILE'])->willReturn($this->mobileChannel);

        $this->factory = new PixelExampleFactory($pixelFactory->reveal(), $channelRepository->reveal());
    }

    /**
     * @test
     */
    public function it_creates_a_pixel_from_options(): void
    {
        $pixel = $this->factory->create([
            'pixel_id' => 123456789,
            'access_token' => 'access_token',
            'enabled' => false,
            'channels' => ['WEB'],
        ]);

        self::assertSame('123456789', $pixel->getPixelId());
        self::assertSame('access_token', $pixel->getAccessToken());
        self::assertFalse($pixel->isEnabled());
        self::assertTrue($pixel->hasChannel($this->webChannel));
        self::assertFalse($pixel->hasChannel($this->mobileChannel));
    }

    /**
     * @test
     */
    public function it_uses_all_channels_and_generates_an_access_token_by_default(): void
    {
        $pixel = $this->factory->create(['pixel_id' => '987654321']);

        self::assertSame('987654321', $pixel->getPixelId());
        self::assertNotNull($pixel->getAccessToken());
        self::assertNotSame('', $pixel->getAccessToken());
        self::assertTrue($pixel->isEnabled());
        self::assertTrue($pixel->hasChannel($this->webChannel));
        self::assertTrue($pixel->hasChannel($this->mobileChannel));
    }

    /**
     * @test
     */
    public function it_requires_a_numeric_pixel_id(): void
    {
        $this->expectException(InvalidOptionsException::class);

        $this->factory->create(['pixel_id' => 'not numeric']);
    }
}
