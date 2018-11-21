<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Entity;

final class FacebookConfigRule implements FacebookConfigRuleInterface
{
    /** @var int */
    protected $id;

    /** @var string */
    protected $insert_pixel_code_here;

    /** @var array */
    protected $configuration = [];

    /** @var FacebookConfigInterface */
    protected $facebookConfig;

    public function getId(): int
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

    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function setConfiguration(array $configuration): void
    {
        $this->configuration = $configuration;
    }

    public function getFacebookConfig(): ?FacebookConfigInterface
    {
        return $this->facebookConfig;
    }

    public function setFacebookConfig(?FacebookConfigInterface $facebookConfig): void
    {
        $this->$facebookConfig = $facebookConfig;
    }
}
