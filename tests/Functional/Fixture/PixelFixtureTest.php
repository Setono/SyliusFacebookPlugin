<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Functional\Fixture;

use Setono\SyliusFacebookPlugin\Fixture\PixelFixture;
use Setono\SyliusFacebookPlugin\Model\PixelInterface;
use Setono\SyliusFacebookPlugin\Tests\Functional\FunctionalTestCase;
use Symfony\Component\Config\Definition\Processor;

final class PixelFixtureTest extends FunctionalTestCase
{
    /**
     * @test
     */
    public function it_loads_pixels_for_the_given_channels(): void
    {
        $channel = self::createChannel('FASHION_WEB');
        self::createChannel('MOBILE');

        $fixture = static::getContainer()->get('setono_sylius_facebook.fixture.pixel');
        self::assertInstanceOf(PixelFixture::class, $fixture);

        $fixture->load((new Processor())->process($fixture->getConfigTreeBuilder()->buildTree(), [[
            'custom' => [
                ['pixel_id' => '123456789', 'access_token' => 'access_token', 'channels' => ['FASHION_WEB']],
                ['pixel_id' => '987654321', 'access_token' => 'access_token', 'enabled' => false, 'channels' => ['FASHION_WEB']],
            ],
        ]]));

        $pixel = self::getPixelRepository()->findOneBy(['pixelId' => '123456789']);
        self::assertInstanceOf(PixelInterface::class, $pixel);
        self::assertTrue($pixel->isEnabled());
        self::assertSame('access_token', $pixel->getAccessToken());
        self::assertSame(['FASHION_WEB'], array_map(static fn ($channel) => $channel->getCode(), $pixel->getChannels()->toArray()));

        $disabledPixel = self::getPixelRepository()->findOneBy(['pixelId' => '987654321']);
        self::assertInstanceOf(PixelInterface::class, $disabledPixel);
        self::assertFalse($disabledPixel->isEnabled());

        self::assertCount(1, self::getPixelRepository()->findEnabledByChannel($channel));
    }
}
