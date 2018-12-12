<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Model;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

interface FacebookConfigInterface extends
    ResourceInterface,
    ToggleableInterface
{
    public function getId(): ?int;

    public function getPixelCode(): ?string;

    public function setPixelCode(string $pixelCode): void;
}
