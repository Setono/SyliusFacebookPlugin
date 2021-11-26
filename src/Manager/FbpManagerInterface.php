<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Manager;

use Symfony\Component\HttpFoundation\Cookie;

interface FbpManagerInterface
{
    public function getFbpCookie(): ?Cookie;

    public function getFbpValue(): ?string;
}
