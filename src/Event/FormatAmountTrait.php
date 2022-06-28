<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Event;

trait FormatAmountTrait
{
    protected static function formatAmount(int $amount): float
    {
        return round($amount / 100, 2);
    }
}
