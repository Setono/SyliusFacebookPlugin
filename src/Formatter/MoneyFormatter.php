<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Formatter;

final class MoneyFormatter
{
    public function format(?int $money): ?float
    {
        if (null === $money) {
            return null;
        }

        return round($money / 100, 2);
    }
}
