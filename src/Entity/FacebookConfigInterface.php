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
    public function getId(): ?int;

    public function getPixelCode(): ?string;

    public function setPixelCode(string $pixelCode): void;

    public function getName(): ?string;

    public function setName(string $name): void;
}
