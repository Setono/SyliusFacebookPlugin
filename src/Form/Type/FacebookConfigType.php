<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Form\Type;

use Setono\SyliusFacebookTrackingPlugin\Model\FacebookConfigInterface;
use Sylius\Bundle\ResourceBundle\Form\Type\AbstractResourceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class FacebookConfigType extends AbstractResourceType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var FacebookConfigInterface $facebookConfig */
        $facebookConfig = $builder->getData();

        $builder
            ->add('pixelCode', TextType::class, [
            'label' => 'setono_sylius_facebook_tracking.ui.pixel_code',
            ])
        ;
    }
}
