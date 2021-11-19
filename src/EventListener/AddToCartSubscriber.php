<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventListener;

use Setono\BotDetectionBundle\BotDetector\BotDetectorInterface;
use Setono\SyliusFacebookPlugin\Context\PixelContextInterface;
use Setono\SyliusFacebookPlugin\Generator\PixelEventsGeneratorInterface;
use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\RequestStack;

final class AddToCartSubscriber extends AbstractSubscriber
{
    protected CartContextInterface $cartContext;

    public function __construct(
        RequestStack $requestStack,
        FirewallMap $firewallMap,
        PixelContextInterface $pixelContext,
        PixelEventsGeneratorInterface $pixelEventsGenerator,
        BotDetectorInterface $botDetector,
        CartContextInterface $cartContext
    ) {
        parent::__construct(
            $requestStack,
            $firewallMap,
            $pixelContext,
            $pixelEventsGenerator,
            $botDetector
        );

        $this->cartContext = $cartContext;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.order_item.post_add' => [
                'track',
            ],
        ];
    }

    public function track(): void
    {
        if (!$this->isRequestEligible() || !$this->pixelContext->hasPixels()) {
            return;
        }

        $order = $this->cartContext->getCart();
        if (!$order instanceof OrderInterface) {
            return;
        }

        $this->pixelEventsGenerator->generatePixelEvents(
            $order,
            ServerSideEventInterface::EVENT_ADD_TO_CART
        );
    }
}
