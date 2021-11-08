<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Formatter;

final class MoneyFormatter implements MoneyFormatterInterface
{
    public function format(?int $money): ?float
    {
        if (null === $money) {
            return null;
        }

        return round($money / 100, 2);
    }
}
