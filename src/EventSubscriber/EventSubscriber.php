<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventSubscriber;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Setono\MetaConversionsApiBundle\Event\ConversionApiEventRaised;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Throwable;

// todo should be renamed to something more descriptive, i.e. 'CatchingEventSubscriber'

/**
 * Since we do not deem Facebook to be 'mission critical', we will catch all errors related to
 * sending an event to Facebook and log it as an error. This way the error won't interfere with any
 * 'real' business, i.e. buying stuff, but it will still be logged correctly, so that developers can act upon it
 */
abstract class EventSubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
    private LoggerInterface $logger;

    private EventDispatcherInterface $eventDispatcher;

    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->logger = new NullLogger();
        $this->eventDispatcher = $eventDispatcher;
    }

    public function track(): void
    {
        try {
            $event = $this->callback()(...func_get_args());
            if (null === $event) {
                return;
            }

            $this->eventDispatcher->dispatch(new ConversionApiEventRaised($event));
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * This callable will receive the event arguments coming from the originating event,
     * and it must return a Setono\MetaConversionsApi\Event\Event
     *
     * @return callable(... mixed): ?\Setono\MetaConversionsApi\Event\Event
     */
    abstract protected function callback(): callable;

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
