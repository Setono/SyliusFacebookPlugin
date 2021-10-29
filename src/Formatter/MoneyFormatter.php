<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Formatter;

final class MoneyFormatter implements MoneyFormatterInterface
{
    public function format(int $money): float
    {
        return round($money / 100, 2);
    }
}
