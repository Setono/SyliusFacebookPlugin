<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Context;

use Setono\SyliusFacebookTrackingPlugin\Model\PixelInterface;

interface PixelContextInterface
{
    /**
     * Returns the pixels enabled for the active channel
     *
     * @return PixelInterface[]
     */
    public function getPixels(): array;
}
