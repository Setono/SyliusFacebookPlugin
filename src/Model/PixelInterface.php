<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Model;

use Sylius\Component\Channel\Model\ChannelsAwareInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\ToggleableInterface;

interface PixelInterface extends ResourceInterface, ToggleableInterface, ChannelsAwareInterface
{
    public function getId(): ?int;

    public function getPixelId(): ?string;

    public function setPixelId(string $pixelId): void;

    public function getCustomAccessToken(): ?string;

    public function setCustomAccessToken(?string $customAccessToken): void;
}
