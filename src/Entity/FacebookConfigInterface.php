<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;
use Sylius\Component\Resource\Model\TranslatableInterface;

interface FacebookConfigInterface extends
    ResourceInterface,
    ToggleableInterface,
    TranslatableInterface
{
    public function getId(): int;

    public function getInsertPixelCodeHere(): ?string;

    public function setInsertPixelCodeHere(string $insert_pixel_code_here): void;
}