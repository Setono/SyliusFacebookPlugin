<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventSubscriber;

use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Setono\MetaConversionsApi\Event\Event;
use Setono\MetaConversionsApiBundle\Event\ConversionsApiEventRaised;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Throwable;

// todo should be renamed to something more descriptive, i.e. 'CatchingEventSubscriber'

/**
 * Since we do not deem Facebook to be 'mission critical', we will catch all errors related to
 * sending an event to Facebook and log it as an error. This way the error won't interfere with any
 * 'real' business, i.e. buying stuff, but it will still be logged correctly, so that developers can act upon it
 *
 * @template TEvent of object
 */
abstract class EventSubscriber implements EventSubscriberInterface, LoggerAwareInterface
{
    private LoggerInterface $logger;

    public function __construct(private readonly EventDispatcherInterface $eventDispatcher)
    {
        $this->logger = new NullLogger();
    }

    /**
     * @param TEvent $event
     */
    public function track(object $event): void
    {
        try {
            $conversionsApiEvent = $this->callback()($event);
            if (null === $conversionsApiEvent) {
                return;
            }

            $this->eventDispatcher->dispatch(new ConversionsApiEventRaised($conversionsApiEvent));
        } catch (Throwable $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * This callable will receive the event from the originating event dispatch,
     * and it must return a Setono\MetaConversionsApi\Event\Event (or null if nothing should be tracked)
     *
     * @return callable(TEvent): ?Event
     */
    abstract protected function callback(): callable;

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
