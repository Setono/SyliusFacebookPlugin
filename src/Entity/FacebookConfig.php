<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Entity;

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ToggleableTrait;
use Sylius\Component\Resource\Model\TranslatableTrait;
use Sylius\Component\Resource\Model\TranslationInterface;

class FacebookConfig implements FacebookConfigInterface
{
    use ToggleableTrait;
    use TranslatableTrait {
        __construct as protected initializeTranslationsCollection;
    }
    public function __construct()
    {
        $this->initializeTranslationsCollection();
    }

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

    public function setInsertPixelCodeHere(string $insert_pixel_code_here): void
    {
        $this->insert_pixel_code_here = $insert_pixel_code_here;
    }

    /**
     * @return FacebookConfigTranslationInterface|TranslationInterface
     */
    protected function getFacebookConfigTranslation(): TranslationInterface
    {
        return $this->getTranslation();
    }

    protected function createTranslation(): FacebookConfigTranslation
    {
        return new FacebookConfigTranslation();
    }
}