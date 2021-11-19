<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Manager;

use Symfony\Component\HttpFoundation\Cookie;

interface FbcManagerInterface
{
    public function getFbcCookie(): ?Cookie;

    public function getFbcValue(): ?string;
}
