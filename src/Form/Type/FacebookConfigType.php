<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Form\Type;

use Setono\SyliusFacebookTrackingPlugin\Entity\FacebookConfigInterface;
use Setono\SyliusFacebookTrackingPlugin\Form\Type\Translation\FacebookConfigTranslationType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Sylius\Bundle\ResourceBundle\Form\Type\ResourceTranslationsType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Valid;

final class FacebookConfigType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var FacebookConfigInterface $facebookConfig */
        $facebookConfig = $builder->getData();

        $builder
            ->add('insert_pixel_code_here', TextType::class, [
                'label' => 'setono_sylius_facebook_tracking_plugin.ui.insert_pixel_code_here',
            ])
            ->add('translations', ResourceTranslationsType::class, [
                'label' => 'setono_sylius_facebook_tracking_plugin.ui.name',
                'entry_type' => FacebookConfigTranslationType::class,
                'validation_groups' => ['setono'],
                'constraints' => [new Valid()],
            ])
        ;
    }
}
