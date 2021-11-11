<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Form\Type;

use Sylius\Bundle\ChannelBundle\Form\Type\ChannelChoiceType;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;

final class PixelType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pixelId', IntegerType::class, [
                'label' => 'setono_sylius_facebook.form.pixel.pixel_id',
                'help' => 'setono_sylius_facebook.form.pixel.pixel_id_help',
                'attr' => [
                    'min' => 1,
                    'placeholder' => 'setono_sylius_facebook.form.pixel.pixel_id_placeholder',
                ],
            ])
            ->add('customAccessToken', TextareaType::class, [
                'label' => 'setono_sylius_facebook.form.pixel.custom_access_token',
                'help' => 'setono_sylius_facebook.form.pixel.custom_access_token_help',
                'required' => false,
                'attr' => [
                    'rows' => 3,
                    'placeholder' => 'setono_sylius_facebook.form.pixel.custom_access_token_placeholder',
                ],
            ])
            ->add('enabled', CheckboxType::class, [
                'required' => false,
                'label' => 'sylius.ui.enabled',
            ])
            ->add('channels', ChannelChoiceType::class, [
                'multiple' => true,
                'expanded' => true,
                'label' => 'sylius.ui.channels',
            ])
        ;
    }

    public function getBlockPrefix(): string
    {
        return 'setono_sylius_facebook_pixel';
    }
}
