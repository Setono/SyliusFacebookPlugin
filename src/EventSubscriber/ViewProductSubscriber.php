<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventSubscriber;

use Setono\SyliusFacebookPlugin\Event\ProductViewedEvent;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\ProductInterface;
use Webmozart\Assert\Assert;

final class ViewProductSubscriber extends EventSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.product.show' => 'track',
        ];
    }

    protected function callback(): callable
    {
        return static function (ResourceControllerEvent $event): ProductViewedEvent {
            $product = $event->getSubject();
            Assert::isInstanceOf($product, ProductInterface::class);

            return new ProductViewedEvent($product);
        };
    }
}
