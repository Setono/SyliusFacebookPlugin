<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Entity;

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
    protected $pixelCode;

    public function getName(): ?string
    {
        return $this->getFacebookConfigTranslation()->getName();
    }

    public function setName(?string $name): void
    {
        $this->getFacebookConfigTranslation()->setName($name);
    }

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
