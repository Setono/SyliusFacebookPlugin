<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Factory;

use Setono\SyliusFacebookPlugin\Model\PixelEventInterface;
use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

interface PixelEventFactoryInterface extends FactoryInterface
{
    public function createFromServerSideEvent(ServerSideEventInterface $event): PixelEventInterface;
}
