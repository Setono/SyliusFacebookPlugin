<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Entity;

use Sylius\Component\Resource\Model\AbstractTranslation;

class FacebookConfigTranslation extends AbstractTranslation implements FacebookConfigTranslationInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $insert_pixel_code_here;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getInsertPixelCodeHere(): string
    {
        return $this->insert_pixel_code_here;
    }

    public function setInsertPixelCodeHere(string $insert_pixel_code_here): void
    {
        $this->insert_pixel_code_here = $insert_pixel_code_here;
    }
}
