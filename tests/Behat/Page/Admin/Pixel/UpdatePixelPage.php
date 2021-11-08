<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusFacebookPlugin\Behat\Page\Admin\Pixel;

use Sylius\Behat\Page\Admin\Crud\UpdatePage;

class UpdatePixelPage extends UpdatePage
{
    public function specifyTrackingId($id): void
    {
        $this->getElement('pixel_id')->setValue($id);
    }

    public function getPixelId(): string
    {
        return $this->getElement('pixel_id')->getValue();
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'pixel_id' => '#setono_sylius_facebook_pixel_pixelId',
        ]);
    }
}
