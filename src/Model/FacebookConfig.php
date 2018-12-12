<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Model;

use Sylius\Component\Resource\Model\ToggleableTrait;

class FacebookConfig implements FacebookConfigInterface
{
    use ToggleableTrait;

    /** @var int */
    protected $id;

    /** @var string|null */
    protected $pixelCode;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPixelCode(): ?string
    {
        return $this->pixelCode;
    }

    public function setPixelCode(?string $pixelCode): void
    {
        $this->pixelCode = $pixelCode;
    }
}
