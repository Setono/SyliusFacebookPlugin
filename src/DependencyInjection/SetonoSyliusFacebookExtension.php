<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\DependencyInjection;

use Setono\SyliusFacebookPlugin\Workflow\SendPixelEventWorkflow;
use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;

final class SetonoSyliusFacebookExtension extends AbstractResourceExtension implements PrependExtensionInterface
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        /**
         * @psalm-suppress PossiblyNullArgument
         *
         * @var array{api_version: string, access_token: string, test_event_code: string|null, send_delay: int, cleanup_delay:int, fbc_ttl: int, fbp_ttl: int, driver: string, resources: array} $config
         */
        $config = $this->processConfiguration($this->getConfiguration([], $container), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->setParameter('setono_sylius_facebook.api_version', $config['api_version']);
        $container->setParameter('setono_sylius_facebook.access_token', $config['access_token']);
        $container->setParameter('setono_sylius_facebook.test_event_code', $config['test_event_code']);
        $container->setParameter('setono_sylius_facebook.send_delay', $config['send_delay']);
        $container->setParameter('setono_sylius_facebook.cleanup_delay', $config['cleanup_delay']);
        $container->setParameter('setono_sylius_facebook.fbc_ttl', $config['fbc_ttl']);
        $container->setParameter('setono_sylius_facebook.fbp_ttl', $config['fbp_ttl']);

        $loader->load('services.xml');

        $this->registerResources('setono_sylius_facebook', $config['driver'], $config['resources'], $container);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('framework', [
            'workflows' => SendPixelEventWorkflow::getConfig(),
        ]);
    }
}
