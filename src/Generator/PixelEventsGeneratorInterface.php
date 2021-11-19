<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Generator;

use Symfony\Component\HttpFoundation\Request;

interface PixelEventsGeneratorInterface
{
    /**
     * @param object $source
     */
    public function generatePixelEvents($source, string $eventName, Request $request = null): void;
}
