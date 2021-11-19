<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventListener;

use Setono\SyliusFacebookPlugin\Context\PixelContextInterface;
use Setono\SyliusFacebookPlugin\Generator\PixelEventsGeneratorInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractSubscriber implements EventSubscriberInterface
{
    protected RequestStack $requestStack;

    protected FirewallMap $firewallMap;

    protected PixelContextInterface $pixelContext;

    protected PixelEventsGeneratorInterface $pixelEventsGenerator;

    public function __construct(
        RequestStack $requestStack,
        FirewallMap $firewallMap,
        PixelContextInterface $pixelContext,
        PixelEventsGeneratorInterface $pixelEventsGenerator
    ) {
        $this->requestStack = $requestStack;
        $this->firewallMap = $firewallMap;
        $this->pixelContext = $pixelContext;
        $this->pixelEventsGenerator = $pixelEventsGenerator;
    }

    protected function isShopContext(Request $request = null): bool
    {
        if (null === $request) {
            $request = $this->requestStack->getCurrentRequest();
            if (null === $request) {
                return true;
            }
        }

        $firewallConfig = $this->firewallMap->getFirewallConfig($request);
        if (null === $firewallConfig) {
            return true;
        }

        return $firewallConfig->getName() === 'shop';
    }

    protected function isRequestEligible(): bool
    {
        // As one main request can have multiple subrequests
        // we don't want things to be tracked multiple times
        // So, having in mind that `If current Request is the master request, it returns null`
        // we expect getParentRequest to be null to proceed
        if (null !== $this->requestStack->getParentRequest()) {
            return false;
        }

        return $this->isShopContext();
    }
}
