<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Webmozart\Assert\Assert;

final class RegisterDataMappersPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('setono_sylius_facebook.data_mapper.composite')) {
            return;
        }

        $dataMapper = $container->getDefinition('setono_sylius_facebook.data_mapper.composite');

        /**
         * @var string $id
         * @var array<array-key, array> $tags
         */
        foreach ($container->findTaggedServiceIds('setono_sylius_facebook.data_mapper') as $id => $tags) {
            foreach ($tags as $tag) {
                /** @var int|mixed $priority */
                $priority = $tag['priority'] ?? 0;
                Assert::integer($priority);

                $dataMapper->addMethodCall('add', [new Reference($id), $priority]);
            }
        }
    }
}
