<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\Fixture;

use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Setono\SyliusFacebookPlugin\Fixture\PixelFixture;
use Setono\SyliusFacebookPlugin\Model\Pixel;
use Sylius\Bundle\CoreBundle\Fixture\Factory\ExampleFactoryInterface;
use Symfony\Component\Config\Definition\Processor;

final class PixelFixtureTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_has_a_name(): void
    {
        $fixture = new PixelFixture(
            $this->prophesize(ObjectManager::class)->reveal(),
            $this->prophesize(ExampleFactoryInterface::class)->reveal(),
        );

        self::assertSame('setono_facebook_pixel', $fixture->getName());
    }

    /**
     * @test
     */
    public function it_creates_and_persists_the_configured_pixels(): void
    {
        $pixel = new Pixel();

        $objectManager = $this->prophesize(ObjectManager::class);
        $objectManager->persist($pixel)->shouldBeCalledOnce();
        $objectManager->flush()->shouldBeCalled();
        $objectManager->clear()->shouldBeCalled();

        $exampleFactory = $this->prophesize(ExampleFactoryInterface::class);
        $exampleFactory->create([
            'pixel_id' => '123456789',
            'access_token' => 'access_token',
            'enabled' => true,
            'channels' => ['WEB'],
        ])->willReturn($pixel)->shouldBeCalledOnce();

        $fixture = new PixelFixture($objectManager->reveal(), $exampleFactory->reveal());

        $fixture->load($this->processOptions($fixture, [
            'custom' => [
                ['pixel_id' => '123456789', 'access_token' => 'access_token', 'channels' => ['WEB']],
            ],
        ]));
    }

    /**
     * @param array<string, mixed> $options
     *
     * @return array<array-key, mixed>
     */
    private function processOptions(PixelFixture $fixture, array $options): array
    {
        return (new Processor())->process($fixture->getConfigTreeBuilder()->buildTree(), [$options]);
    }
}
