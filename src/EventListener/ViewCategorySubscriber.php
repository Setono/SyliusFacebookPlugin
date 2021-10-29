<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventListener;

use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Bundle\ResourceBundle\Grid\View\ResourceGridView;

/**
 * See https://developers.facebook.com/docs/marketing-api/audiences/guides/dynamic-product-audiences/#setuppixel
 * for reference of the 'ViewCategory' custom event
 */
final class ViewCategorySubscriber extends AbstractSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.product.index' => [
                'trackCustom',
            ],
        ];
    }

    public function trackCustom(ResourceControllerEvent $event): void
    {
        if (!$this->isShopContext() || !$this->pixelContext->hasPixels()) {
            return;
        }

        $gridView = $event->getSubject();
        if (!$gridView instanceof ResourceGridView) {
            return;
        }

        // @todo trackCustom???
        $this->generatePixelEvents(
            $gridView,
            ServerSideEventInterface::CUSTOM_EVENT_VIEW_CATEGORY
        );
    }
}
