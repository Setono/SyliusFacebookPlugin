<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\EventListener;

use function count;
use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusFacebookTrackingPlugin\Context\PixelContextInterface;
use Setono\SyliusFacebookTrackingPlugin\Formatter\MoneyFormatter;
use Setono\TagBag\TagBagInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class TagSubscriber implements EventSubscriberInterface
{
    /** @var array|null */
    private $pixels;

    /** @var TagBagInterface */
    protected $tagBag;

    /** @var PixelContextInterface */
    private $pixelContext;

    /** @var EventDispatcherInterface */
    protected $eventDispatcher;

    /** @var MoneyFormatter */
    protected $moneyFormatter;

    /** @var RequestStack */
    private $requestStack;

    /** @var FirewallMap */
    private $firewallMap;

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
        if (null === $this->pixels) {
            $this->pixels = $this->pixelContext->getPixels();
        }

        return $this->pixels;
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
