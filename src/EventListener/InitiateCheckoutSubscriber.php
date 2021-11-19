<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventListener;

use Setono\BotDetectionBundle\BotDetector\BotDetectorInterface;
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
            KernelEvents::REQUEST => 'track',
        ];
    }

    public function track(RequestEvent $requestEvent): void
    {
        if (!$this->isRequestEligible() || !$this->pixelContext->hasPixels()) {
            return;
        }

        $cart = $this->cartContext->getCart();
        if ($cart->isEmpty()) {
            return;
        }

        $this->pixelEventsGenerator->generatePixelEvents(
            $cart,
            ServerSideEventInterface::EVENT_INITIATE_CHECKOUT
        );
    }

    /**
     * Request is eligible when:
     * - We are on the 'checkout start' page
     */
    protected function isRequestEligible(): bool
    {
        if (!parent::isRequestEligible()) {
            return false;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return false;
        }

        return 'sylius_shop_checkout_start' === $request->attributes->get('_route');
    }
}
