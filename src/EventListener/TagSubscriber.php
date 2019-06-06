<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\EventListener;

use function count;
use Setono\SyliusFacebookTrackingPlugin\Context\PixelContextInterface;
use Setono\SyliusFacebookTrackingPlugin\Formatter\MoneyFormatter;
use Setono\TagBagBundle\TagBag\TagBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\LegacyEventDispatcherProxy;

abstract class TagSubscriber implements EventSubscriberInterface
{
    /**
     * @var array|null
     */
    private $pixels;

    /**
     * @var TagBagInterface
     */
    protected $tagBag;

    /**
     * @var PixelContextInterface
     */
    private $pixelContext;

    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;

    /**
     * @var MoneyFormatter
     */
    protected $moneyFormatter;

    public function __construct(TagBagInterface $tagBag, PixelContextInterface $pixelContext, EventDispatcherInterface $eventDispatcher)
    {
        if (class_exists(LegacyEventDispatcherProxy::class)) {
            /**
             * It could return null only if we pass null, but we pass not null in any case
             *
             * @var EventDispatcherInterface
             */
            $eventDispatcher = LegacyEventDispatcherProxy::decorate($eventDispatcher);
        }

        $this->tagBag = $tagBag;
        $this->pixelContext = $pixelContext;
        $this->eventDispatcher = $eventDispatcher;
        $this->moneyFormatter = new MoneyFormatter();
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

    protected function dispatch(string $eventName, $event): void
    {
        if (class_exists(LegacyEventDispatcherProxy::class)) {
            $this->eventDispatcher->dispatch($event, $eventName);
        } else {
            $this->eventDispatcher->dispatch($eventName, $event);
        }
    }
}
