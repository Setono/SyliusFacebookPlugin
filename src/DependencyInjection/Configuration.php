<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\DependencyInjection;

use Setono\SyliusFacebookPlugin\Doctrine\ORM\PixelEventRepository;
use Setono\SyliusFacebookPlugin\Doctrine\ORM\PixelRepository;
use Setono\SyliusFacebookPlugin\Form\Type\PixelType;
use Setono\SyliusFacebookPlugin\Model\Pixel;
use Setono\SyliusFacebookPlugin\Model\PixelEvent;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;
use Sylius\Component\Resource\Factory\Factory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('setono_sylius_facebook');

        /** @var ArrayNodeDefinition $rootNode */
        $rootNode = $treeBuilder->getRootNode();

        /** @psalm-suppress MixedMethodCall, PossiblyUndefinedMethod, PossiblyNullReference */
        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->scalarNode('driver')
                    ->defaultValue(SyliusResourceBundle::DRIVER_DOCTRINE_ORM)
                ->end()
                ->scalarNode('api_version')
                    ->defaultValue('v13.0')
                    ->info('Facebook API version')
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('access_token')
                    ->info('Your ACCESS_TOKEN')
                    ->isRequired()
                    ->cannotBeEmpty()
                ->end()
                ->scalarNode('test_event_code')
                    ->info('Your test_event_code (required for debugging)')
                    ->defaultNull()
                ->end()
                ->integerNode('send_delay')
                    ->defaultValue(300) // 5 minutes
                    ->info('The number of seconds to wait until an event is sent to Facebook')
                    ->example(120) // 2 minutes
                ->end()
                ->integerNode('cleanup_delay')
                    ->defaultValue(30 * 24 * 60 * 60) // 30 days
                    ->info('The number of seconds to wait until remove sent event')
                ->end()
                ->integerNode('fbc_ttl')
                    ->defaultValue(28 * 24 * 60 * 60) // 28 days
                    ->info('Time to live for fbc cookie')
                ->end()
                ->integerNode('fbp_ttl')
                    ->defaultValue(365 * 24 * 60 * 60) // 365 days
                    ->info('Time to live for fbp cookie')
                ->end()
            ->end()
        ;

        $this->addResourcesSection($rootNode);

        return $treeBuilder;
    }

    private function addResourcesSection(ArrayNodeDefinition $node): void
    {
        /** @psalm-suppress MixedMethodCall, PossiblyUndefinedMethod, PossiblyNullReference */
        $node
            ->children()
                ->arrayNode('resources')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->arrayNode('pixel')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(Pixel::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(PixelRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                        ->scalarNode('form')->defaultValue(PixelType::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                        ->arrayNode('pixel_event')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->variableNode('options')->end()
                                ->arrayNode('classes')
                                    ->addDefaultsIfNotSet()
                                    ->children()
                                        ->scalarNode('model')->defaultValue(PixelEvent::class)->cannotBeEmpty()->end()
                                        ->scalarNode('controller')->defaultValue(ResourceController::class)->cannotBeEmpty()->end()
                                        ->scalarNode('repository')->defaultValue(PixelEventRepository::class)->cannotBeEmpty()->end()
                                        ->scalarNode('factory')->defaultValue(Factory::class)->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;
    }
}
