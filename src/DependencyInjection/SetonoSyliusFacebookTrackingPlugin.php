<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\DependencyInjection;

use Sylius\Bundle\ResourceBundle\DependencyInjection\Extension\AbstractResourceExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class SetonoSyliusFacebookTrackingPlugin extends AbstractResourceExtension
{
    public function load(array $config, ContainerBuilder $container)
    {
        $config = $this->processConfiguration($this->getConfiguration([], $container), $config);
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $container->setParameter('setono.sylius_facebook_tracking_plugin.facebook_config', $config['facebook_config']);

        $loader->load('services.yml');

        $this->registerResources('setono_sylius_facebook', $config['driver'], $config['resources'], $container);
    }

}
