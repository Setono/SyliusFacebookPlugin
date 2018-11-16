<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Entity;

final class FacebookConfig implements FacebookConfigInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $insert_pixel_code_here;

    public function getId(): int {
        return $this->id;
    }

    public function getInsertPixelCodeHere(): string
    {
        return $this->insert_pixel_code_here;
    }

    public function setInsertPixelCodeHere(string $insert_pixel_code_here): void {
        $this->insert_pixel_code_here = $insert_pixel_code_here;
    }
}