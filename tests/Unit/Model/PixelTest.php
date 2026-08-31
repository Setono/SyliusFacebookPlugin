<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\Model;

use PHPUnit\Framework\TestCase;
use Setono\SyliusFacebookPlugin\Model\Pixel;
use Sylius\Component\Channel\Model\Channel;

final class PixelTest extends TestCase
{
    /**
     * @test
     */
    public function it_is_enabled_by_default_and_has_no_channels(): void
    {
        $pixel = new Pixel();

        self::assertNull($pixel->getId());
        self::assertNull($pixel->getPixelId());
        self::assertNull($pixel->getAccessToken());
        self::assertTrue($pixel->isEnabled());
        self::assertCount(0, $pixel->getChannels());
    }

    /**
     * @test
     */
    public function it_casts_to_string_using_the_pixel_id(): void
    {
        $pixel = new Pixel();
        self::assertSame('', (string) $pixel);

        $pixel->setPixelId('123456789');
        self::assertSame('123456789', (string) $pixel);
    }

    /**
     * @test
     */
    public function it_adds_a_channel_only_once(): void
    {
        $channel = new Channel();

        $pixel = new Pixel();
        $pixel->addChannel($channel);
        $pixel->addChannel($channel);

        self::assertTrue($pixel->hasChannel($channel));
        self::assertCount(1, $pixel->getChannels());
    }

    /**
     * @test
     */
    public function it_removes_a_channel(): void
    {
        $channel = new Channel();
        $otherChannel = new Channel();

        $pixel = new Pixel();
        $pixel->addChannel($channel);
        $pixel->addChannel($otherChannel);

        $pixel->removeChannel($channel);
        $pixel->removeChannel($channel);

        self::assertFalse($pixel->hasChannel($channel));
        self::assertTrue($pixel->hasChannel($otherChannel));
        self::assertCount(1, $pixel->getChannels());
    }

    /**
     * @test
     */
    public function it_has_an_access_token(): void
    {
        $pixel = new Pixel();
        $pixel->setAccessToken('access_token');
        self::assertSame('access_token', $pixel->getAccessToken());

        $pixel->setAccessToken(null);
        self::assertNull($pixel->getAccessToken());
    }
}
