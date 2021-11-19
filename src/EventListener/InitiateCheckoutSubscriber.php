<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventListener;

use Setono\SyliusFacebookPlugin\Context\PixelContextInterface;
use Setono\SyliusFacebookPlugin\Generator\PixelEventsGeneratorInterface;
use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class InitiateCheckoutSubscriber extends AbstractSubscriber
{
    protected CartContextInterface $cartContext;

    public function __construct(
        RequestStack $requestStack,
        FirewallMap $firewallMap,
        PixelContextInterface $pixelContext,
        PixelEventsGeneratorInterface $pixelEventsGenerator,
        CartContextInterface $cartContext
    ) {
        parent::__construct($requestStack, $firewallMap, $pixelContext, $pixelEventsGenerator);

        $this->cartContext = $cartContext;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'track',
        ];
    }

    public function track(RequestEvent $requestEvent): void
    {
        $request = $requestEvent->getRequest();

        if (!$requestEvent->isMasterRequest()) {
            return;
        }

        if (!$request->attributes->has('_route')) {
            return;
        }

        $route = $request->attributes->get('_route');
        if ('sylius_shop_checkout_start' !== $route) {
            return;
        }

        $cart = $this->cartContext->getCart();
        if ($cart->isEmpty()) {
            return;
        }

        if (!$this->pixelContext->hasPixels()) {
            return;
        }

        $this->pixelEventsGenerator->generatePixelEvents(
            $cart,
            ServerSideEventInterface::EVENT_INITIATE_CHECKOUT
        );
    }
}
