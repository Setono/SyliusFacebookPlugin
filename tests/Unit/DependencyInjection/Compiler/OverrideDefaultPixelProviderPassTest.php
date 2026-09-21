<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit\DependencyInjection\Compiler;

use PHPUnit\Framework\TestCase;
use Setono\MetaConversionsApiBundle\Provider\PixelProviderInterface;
use Setono\SyliusFacebookPlugin\DependencyInjection\Compiler\OverrideDefaultPixelProviderPass;
use Setono\SyliusFacebookPlugin\Provider\DoctrineBasedPixelProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

final class OverrideDefaultPixelProviderPassTest extends TestCase
{
    /**
     * @test
     */
    public function it_aliases_the_default_pixel_provider_to_the_doctrine_based_pixel_provider(): void
    {
        $container = new ContainerBuilder();
        $container->setDefinition(
            'setono_sylius_facebook.provider.doctrine_based_pixel_provider',
            new Definition(DoctrineBasedPixelProvider::class),
        );

        (new OverrideDefaultPixelProviderPass())->process($container);

        self::assertTrue($container->hasAlias(PixelProviderInterface::class));
        self::assertSame(
            'setono_sylius_facebook.provider.doctrine_based_pixel_provider',
            (string) $container->getAlias(PixelProviderInterface::class),
        );
    }

    /**
     * @test
     */
    public function it_does_nothing_when_the_doctrine_based_pixel_provider_is_not_defined(): void
    {
        $container = new ContainerBuilder();

        (new OverrideDefaultPixelProviderPass())->process($container);

        self::assertFalse($container->hasAlias(PixelProviderInterface::class));
    }
}
