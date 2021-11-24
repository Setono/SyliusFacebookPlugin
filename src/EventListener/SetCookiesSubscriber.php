<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventListener;

use Setono\BotDetectionBundle\BotDetector\BotDetectorInterface;
use Setono\SyliusFacebookPlugin\Context\PixelContextInterface;
use Setono\SyliusFacebookPlugin\Generator\PixelEventsGeneratorInterface;
use Setono\SyliusFacebookPlugin\Manager\FbcManagerInterface;
use Setono\SyliusFacebookPlugin\Manager\FbpManagerInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class SetCookiesSubscriber extends AbstractSubscriber
{
    private FbcManagerInterface $fbcManager;

    private FbpManagerInterface $fbpManager;

    public function __construct(
        RequestStack $requestStack,
        FirewallMap $firewallMap,
        PixelContextInterface $pixelContext,
        PixelEventsGeneratorInterface $pixelEventsGenerator,
        BotDetectorInterface $botDetector,
        FbcManagerInterface $fbcManager,
        FbpManagerInterface $fbpManager
    ) {
        parent::__construct(
            $requestStack,
            $firewallMap,
            $pixelContext,
            $pixelEventsGenerator,
            $botDetector
        );

        $this->fbcManager = $fbcManager;
        $this->fbpManager = $fbpManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::RESPONSE => 'setCookies',
        ];
    }

    public function setCookies(ResponseEvent $event): void
    {
        if (!$this->isRequestEligible()) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request || $request->isXmlHttpRequest()) {
            return;
        }

        $response = $event->getResponse();
        $fbcCookie = $this->fbcManager->getFbcCookie();
        if (null !== $fbcCookie) {
            $response->headers->setCookie($fbcCookie);
        }

        $fbpCookie = $this->fbpManager->getFbpCookie();
        if (null !== $fbpCookie) {
            $response->headers->setCookie($fbpCookie);
        }
    }
}
