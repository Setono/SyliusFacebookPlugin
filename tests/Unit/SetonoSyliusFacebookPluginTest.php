<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tests\Unit;

use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use PHPUnit\Framework\TestCase;
use Setono\SyliusFacebookPlugin\DependencyInjection\Compiler\OverrideDefaultPixelProviderPass;
use Setono\SyliusFacebookPlugin\DependencyInjection\SetonoSyliusFacebookExtension;
use Setono\SyliusFacebookPlugin\SetonoSyliusFacebookPlugin;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

final class SetonoSyliusFacebookPluginTest extends TestCase
{
    /**
     * @test
     */
    public function it_supports_the_doctrine_orm_driver(): void
    {
        self::assertSame([SyliusResourceBundle::DRIVER_DOCTRINE_ORM], (new SetonoSyliusFacebookPlugin())->getSupportedDrivers());
    }

    /**
     * @test
     */
    public function it_has_a_container_extension(): void
    {
        self::assertInstanceOf(SetonoSyliusFacebookExtension::class, (new SetonoSyliusFacebookPlugin())->getContainerExtension());
    }

    /**
     * @test
     */
    public function it_registers_the_doctrine_mapping_and_the_pixel_provider_compiler_passes(): void
    {
        $container = new ContainerBuilder();

        (new SetonoSyliusFacebookPlugin())->build($container);

        $passes = array_map(
            static fn (object $pass): string => $pass::class,
            $container->getCompilerPassConfig()->getBeforeOptimizationPasses(),
        );

        self::assertContains(DoctrineOrmMappingsPass::class, $passes);
        self::assertContains(OverrideDefaultPixelProviderPass::class, $passes);
    }
}
