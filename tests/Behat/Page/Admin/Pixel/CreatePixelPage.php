<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusFacebookTrackingPlugin\Behat\Page\Admin\Pixel;

use Sylius\Behat\Page\Admin\Crud\CreatePage as BaseCreatePage;

class CreatePixelPage extends BaseCreatePage
{
    public function specifyPixelId($id): void
    {
        $this->getElement('pixel_id')->setValue($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'pixel_id' => '#setono_sylius_facebook_tracking_pixel_pixelId',
        ]);
    }
}
