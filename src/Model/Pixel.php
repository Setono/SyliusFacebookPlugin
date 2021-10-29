<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Channel\Model\ChannelInterface as BaseChannelInterface;
use Sylius\Component\Resource\Model\ToggleableTrait;

class Pixel implements PixelInterface
{
    use ToggleableTrait;

    protected ?int $id = null;

    protected ?string $pixelId = null;

    /** @var Collection|BaseChannelInterface[] */
    protected Collection $channels;

    public function __toString()
    {
        return $this->getPixelId() ?? '';
    }

    public function __construct()
    {
        $this->channels = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPixelId(): ?string
    {
        return $this->pixelId;
    }

    public function setPixelId(string $pixelId): void
    {
        $this->pixelId = $pixelId;
    }

    public function getChannels(): Collection
    {
        return $this->channels;
    }

    public function addChannel(BaseChannelInterface $channel): void
    {
        if (!$this->hasChannel($channel)) {
            $this->channels->add($channel);
        }
    }

    public function removeChannel(BaseChannelInterface $channel): void
    {
        if ($this->hasChannel($channel)) {
            $this->channels->removeElement($channel);
        }
    }

    public function hasChannel(BaseChannelInterface $channel): bool
    {
        return $this->channels->contains($channel);
    }
}
