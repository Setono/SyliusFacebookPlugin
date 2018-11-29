<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusFacebookTrackingPlugin\Behat\Page\Admin\FacebookConfig;

use Sylius\Behat\Page\Admin\Crud\UpdatePage as BaseUpdatePage;

final class UpdatePage extends BaseUpdatePage implements UpdatePageInterface
{
    public function fillCode(?string $code): void
    {
        $this->getDocument()->fillField('facebook_config_pixelCode', $code);
    }

    public function fillName(?string $name): void
    {
        $this->getDocument()->fillField('facebook_config_translations_en_US_name', $name);
    }

}
