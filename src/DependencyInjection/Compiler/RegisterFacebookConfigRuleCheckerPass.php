<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class RegisterFacebookConfigRuleCheckerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if(!$container->has('setono_sylius_facebook_tracking_plugin.registry_facebook_config_rule_checker')
            || !$container->has('setono_sylius_facebook_tracking_plugin.form_registry.facebook_config_rule_checker')) {
            return;
        };

        $facebookConfigRuleCheckerRegsitry = $container->getDefinition('setono_sylius_facebook_tracking_plugin.registry_facebook_config_rule_checker');
        $facebookConfigRuleCheckerFormTypeRegsitry = $container->getDefinition('setono_sylius_facebook_tracking_plugin.form_registry.facebook_config_rule_checker');

        $facebookConfigRuleCheckerTypeToLabelMap = [];
        foreach ($container->findTaggedServiceIds('setono_sylius_facebook_tracking_plugin.facebook_config_rule_checker') as $id => $attributes) {
            if (!isset($attributes[0]['type'], $attributes[0]['label'], $attributes[0]['form_type'])) {
                throw new \InvalidArgumentException('Tagged rule checker `' . $id . '` needs to have `type`, `form_type` and `label` attributes.');
            }
        }

        $facebookConfigRuleCheckerTypeToLabelMap[$attributes[0]['type']] = $attributes[0]['label'];
        $facebookConfigRuleCheckerRegsitry->addMethodCall('register', [$attributes[0]['type'], new Reference($id)]);
        $facebookConfigRuleCheckerFormTypeRegsitry->addMethodCall('add', [$attributes[0]['type'], 'default', $attributes[0]['form_type']]);
    }
}