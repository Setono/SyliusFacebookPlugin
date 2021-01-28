<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventListener;

use function count;
use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusFacebookPlugin\Context\PixelContextInterface;
use Setono\SyliusFacebookPlugin\Formatter\MoneyFormatter;
use Setono\TagBag\TagBagInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class TagSubscriber implements EventSubscriberInterface
{
    protected TagBagInterface $tagBag;

    private PixelContextInterface $pixelContext;

    protected EventDispatcherInterface $eventDispatcher;

    protected MoneyFormatter $moneyFormatter;

    private RequestStack $requestStack;

    private FirewallMap $firewallMap;

    public function __construct(
        TagBagInterface $tagBag,
        PixelContextInterface $pixelContext,
        EventDispatcherInterface $eventDispatcher,
        RequestStack $requestStack,
        FirewallMap $firewallMap
    ) {
        $this->tagBag = $tagBag;
        $this->pixelContext = $pixelContext;
        $this->eventDispatcher = $eventDispatcher;
        $this->moneyFormatter = new MoneyFormatter();
        $this->requestStack = $requestStack;
        $this->firewallMap = $firewallMap;
    }

    protected function hasPixels(): bool
    {
        return count($this->getPixels()) > 0;
    }

    protected function getPixels(): array
    {
        return $this->pixelContext->getPixels();
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
}
