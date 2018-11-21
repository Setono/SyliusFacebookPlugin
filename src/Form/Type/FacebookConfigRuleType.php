<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Form\Type;

use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\FormBuilderInterface;

final class FacebookConfigRuleType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options = []): void
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('type', FacebookConfigRuleChoiceType::class, [
                'label' => 'setono_sylius_facebook_tracking_plugin.form.facebook_config_rule.type',
                'attr' => [
                    'data-form-collection' => 'update',
                ],
            ])
        ;
    }
}
