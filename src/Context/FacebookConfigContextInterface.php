<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Context;

use Setono\SyliusFacebookTrackingPlugin\Entity\FacebookConfigInterface;

interface FacebookConfigContextInterface
{
    public const DEFAULT_CODE = 'default';

    public function getConfig(): FacebookConfigInterface;
}