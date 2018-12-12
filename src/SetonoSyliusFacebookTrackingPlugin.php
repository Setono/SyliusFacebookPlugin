<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Sylius\Bundle\ResourceBundle\AbstractResourceBundle;
use Sylius\Bundle\ResourceBundle\SyliusResourceBundle;

final class SetonoSyliusFacebookTrackingPlugin extends AbstractResourceBundle
{
    use SyliusPluginTrait;

    /**
     * {@inheritdoc}
     */
    public function getSupportedDrivers(): array
    {
        return [
            SyliusResourceBundle::DRIVER_DOCTRINE_ORM,
        ];
    }

    protected function getModelNamespace(): string
    {
        return 'Setono\SyliusFacebookTrackingPlugin\Model';
    }
}
