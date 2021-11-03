<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Client;

use Setono\SyliusFacebookPlugin\Model\PixelEventInterface;

interface ClientInterface
{
    public function sendPixelEvent(PixelEventInterface $pixelEvent): int;
}
