<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Context;

use Setono\SyliusFacebookPlugin\Model\PixelInterface;

interface PixelContextInterface
{
    /**
     * Returns the pixels enabled for the active channel
     *
     * @return array<array-key, PixelInterface>
     */
    public function getPixels(): array;

    public function hasPixels(): bool;
}
