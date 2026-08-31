<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Functional\Repository;

use Setono\SyliusFacebookPlugin\Model\PixelInterface;
use Setono\SyliusFacebookPlugin\Tests\Functional\FunctionalTestCase;

final class PixelRepositoryTest extends FunctionalTestCase
{
    /**
     * @test
     */
    public function it_finds_the_enabled_pixels_of_a_channel(): void
    {
        $web = self::createChannel('WEB');
        $mobile = self::createChannel('MOBILE');

        self::createPixel('1000', true, $web);
        self::createPixel('2000', false, $web);
        self::createPixel('3000', true, $mobile);
        self::createPixel('4000', true, $web, $mobile);
        self::createPixel('5000', true);

        self::assertSame(['1000', '4000'], self::pixelIds(self::getPixelRepository()->findEnabledByChannel($web)));
        self::assertSame(['3000', '4000'], self::pixelIds(self::getPixelRepository()->findEnabledByChannel($mobile)));
    }

    /**
     * @test
     */
    public function it_finds_no_pixels_for_a_channel_without_pixels(): void
    {
        $channel = self::createChannel('WEB');

        self::assertSame([], self::getPixelRepository()->findEnabledByChannel($channel));
    }

    /**
     * @param array<array-key, PixelInterface> $pixels
     *
     * @return list<string>
     */
    private static function pixelIds(array $pixels): array
    {
        $pixelIds = array_map(static fn (PixelInterface $pixel): string => (string) $pixel->getPixelId(), $pixels);
        sort($pixelIds);

        return $pixelIds;
    }
}
