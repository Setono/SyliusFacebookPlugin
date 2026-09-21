<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Functional\Provider;

use Setono\MetaConversionsApi\Pixel\Pixel;
use Setono\MetaConversionsApiBundle\Provider\PixelProviderInterface;
use Setono\SyliusFacebookPlugin\Provider\DoctrineBasedPixelProvider;
use Setono\SyliusFacebookPlugin\Tests\Functional\FunctionalTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class PixelProviderTest extends FunctionalTestCase
{
    /**
     * If this alias is not overridden the bundle falls back to the pixels from its own configuration, which are none,
     * and every event is dropped without an error
     *
     * @test
     */
    public function it_replaces_the_pixel_provider_of_the_meta_conversions_api_bundle(): void
    {
        self::assertInstanceOf(DoctrineBasedPixelProvider::class, self::getPixelProvider());
    }

    /**
     * @test
     */
    public function it_provides_the_enabled_pixels_of_the_channel_of_the_request(): void
    {
        $web = self::createChannel('WEB');
        $web->setHostname('web.example.com');
        $mobile = self::createChannel('MOBILE');
        $mobile->setHostname('mobile.example.com');
        self::persist($web, $mobile);

        self::createPixel('1000', true, $web);
        self::createPixel('2000', false, $mobile);
        self::createPixel('3000', true, $mobile);

        $requestStack = static::getContainer()->get('request_stack');
        self::assertInstanceOf(RequestStack::class, $requestStack);
        $requestStack->push(Request::create('https://mobile.example.com/'));

        self::assertSame(['3000'], self::pixelIds(self::getPixelProvider()->getPixels()));
    }

    /**
     * This is what happens in a Messenger worker: the bundle asks for the pixels to add the access tokens
     * back onto the event it is about to send
     *
     * @test
     */
    public function it_provides_every_enabled_pixel_with_its_access_token_when_there_is_no_request(): void
    {
        $web = self::createChannel('WEB');
        $mobile = self::createChannel('MOBILE');

        self::createPixel('1000', true, $web);
        self::createPixel('2000', false, $web);
        self::createPixel('3000', true, $mobile);

        $pixels = self::getPixelProvider()->getPixels();

        self::assertSame(['1000', '3000'], self::pixelIds($pixels));
        foreach ($pixels as $pixel) {
            self::assertSame('access_token', $pixel->accessToken);
        }
    }

    private static function getPixelProvider(): PixelProviderInterface
    {
        $pixelProvider = static::getContainer()->get(PixelProviderInterface::class);
        self::assertInstanceOf(PixelProviderInterface::class, $pixelProvider);

        return $pixelProvider;
    }

    /**
     * @param list<Pixel> $pixels
     *
     * @return list<string>
     */
    private static function pixelIds(array $pixels): array
    {
        $pixelIds = array_map(static fn (Pixel $pixel): string => $pixel->id, $pixels);
        sort($pixelIds);

        return $pixelIds;
    }
}
