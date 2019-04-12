<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\EventListener;

use Setono\SyliusFacebookTrackingPlugin\Context\PixelContextInterface;
use Setono\SyliusFacebookTrackingPlugin\Formatter\MoneyFormatter;
use Setono\TagBagBundle\TagBag\TagBagInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

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
    protected $eventDispatcher;

    /**
     * @var MoneyFormatter
     */
    protected $moneyFormatter;

    public function __construct(TagBagInterface $tagBag, PixelContextInterface $pixelContext, EventDispatcherInterface $eventDispatcher)
    {
        $this->tagBag = $tagBag;
        $this->pixelContext = $pixelContext;
        $this->eventDispatcher = $eventDispatcher;
        $this->moneyFormatter = new MoneyFormatter();
    }

    protected function hasPixels(): bool
    {
        return \count($this->getPixels()) > 0;
    }

    protected function getPixels(): array
    {
        if (null === $this->pixels) {
            $this->pixels = $this->pixelContext->getPixels();
        }

        return $this->pixels;
    }
}
