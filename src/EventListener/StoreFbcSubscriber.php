<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventListener;

use Setono\BotDetectionBundle\BotDetector\BotDetectorInterface;
use Setono\SyliusFacebookPlugin\Context\PixelContextInterface;
use Setono\SyliusFacebookPlugin\Generator\PixelEventsGeneratorInterface;
use Setono\SyliusFacebookPlugin\Manager\FbcManagerInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class StoreFbcSubscriber extends AbstractSubscriber
{
    private FbcManagerInterface $fbcManager;

    public function __construct(
        RequestStack $requestStack,
        FirewallMap $firewallMap,
        PixelContextInterface $pixelContext,
        PixelEventsGeneratorInterface $pixelEventsGenerator,
        BotDetectorInterface $botDetector,
        FbcManagerInterface $fbcManager
    ) {
        parent::__construct(
            $requestStack,
            $firewallMap,
            $pixelContext,
            $pixelEventsGenerator,
            $botDetector
        );

        $this->fbcManager = $fbcManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'setFbcCookie',
        ];
    }

    public function setFbcCookie(ResponseEvent $event): void
    {
        if (!$this->isRequestEligible()) {
            return;
        }

        $fbcCookie = $this->fbcManager->getFbcCookie();
        if (null === $fbcCookie) {
            return;
        }

        $response = $event->getResponse();
        $response->headers->setCookie($fbcCookie);
    }
}
