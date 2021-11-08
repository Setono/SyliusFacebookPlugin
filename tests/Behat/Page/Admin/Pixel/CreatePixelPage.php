<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusFacebookPlugin\Behat\Page\Admin\Pixel;

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

class CreatePixelPage extends BaseCreatePage
{
    public function specifyPixelId($id): void
    {
        $this->getElement('pixel_id')->setValue($id);
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'pixel_id' => '#setono_sylius_facebook_pixel_pixelId',
        ]);
    }
}
