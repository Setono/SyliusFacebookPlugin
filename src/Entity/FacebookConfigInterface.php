<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Entity;

use Sylius\Component\Resource\Model\ResourceInterface;

interface FacebookConfigInterface extends ResourceInterface
{
    public function getId(): int;
    public function getInsertPixelCodeHere(): string;
    public function setInsertPixelCodeHere(string $insert_pixel_code_here): void;
}