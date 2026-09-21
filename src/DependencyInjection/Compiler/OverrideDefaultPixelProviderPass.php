<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\DependencyInjection\Compiler;

use Setono\MetaConversionsApiBundle\Provider\PixelProviderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * The default pixel provider takes pixel ids and access tokens from the bundle configuration.
 * This compiler pass will override the default pixel provider to use pixels defined inside the Sylius admin
 */
final class OverrideDefaultPixelProviderPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('setono_sylius_facebook.provider.doctrine_based_pixel_provider')) {
            return;
        }

        $container->setAlias(
            PixelProviderInterface::class,
            'setono_sylius_facebook.provider.doctrine_based_pixel_provider',
        );
    }
}
