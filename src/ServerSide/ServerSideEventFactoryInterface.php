<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\ServerSide;

interface ServerSideEventFactoryInterface
{
    public function create(string $eventName): ServerSideEventInterface;
}
