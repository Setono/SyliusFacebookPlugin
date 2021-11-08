<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\ServerSide;

use FacebookAds\Object\ServerSide\ActionSource;

class ServerSideEventFactory implements ServerSideEventFactoryInterface
{
    public function create(string $eventName): ServerSideEventInterface
    {
        return (new ServerSideEvent())
            ->setEventName($eventName)
            ->setEventTime(time())
            ->setActionSource(ActionSource::WEBSITE)
        ;
    }
}
