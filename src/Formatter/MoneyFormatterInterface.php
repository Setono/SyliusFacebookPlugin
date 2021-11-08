<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Formatter;

interface MoneyFormatterInterface
{
    public function format(?int $money): ?float;
}
