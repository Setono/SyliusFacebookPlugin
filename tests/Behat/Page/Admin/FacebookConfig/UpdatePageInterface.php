<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusFacebookTrackingPlugin\Behat\Page\Admin\FacebookConfig;

use Sylius\Behat\Page\Admin\Crud\UpdatePageInterface as BaseUpdatePageInterface;

interface UpdatePageInterface extends BaseUpdatePageInterface
{
    public function fillCode(?string $code): void;
    public function fillName(?string $name): void;
}
