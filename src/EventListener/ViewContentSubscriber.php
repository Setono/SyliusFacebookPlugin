<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventListener;

use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Product\Model\ProductInterface;

final class ViewContentSubscriber extends AbstractSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.product.show' => [
                'track',
            ],
        ];
    }

    public function track(ResourceControllerEvent $event): void
    {
        if (!$this->isRequestEligible() || !$this->pixelContext->hasPixels()) {
            return;
        }

        $product = $event->getSubject();
        if (!$product instanceof ProductInterface) {
            return;
        }

        $this->pixelEventsGenerator->generatePixelEvents(
            $product,
            ServerSideEventInterface::EVENT_VIEW_CONTENT
        );
    }
}
