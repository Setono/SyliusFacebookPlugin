<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

interface FacebookConfigRuleInterface extends ResourceInterface
{
    public function getId(): int;

    public function getInsertPixelCodeHere(): string;

    public function setInsertPixelCodeHere(string $insert_pixel_code_here): void;

    public function getConfiguration(): array;

    public function setConfiguration(array $configuration): void;

    public function getFacebookConfig(): ?FacebookConfigInterface;

    public function setFacebookConfig(?FacebookConfigInterface $facebookConfig): void;
}
