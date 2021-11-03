<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Model;

use DateTimeInterface;
use Setono\ClientId\ClientId;
use Symfony\Component\Uid\Uuid;

class PixelEvent implements PixelEventInterface
{
    protected string $id;

    protected ?PixelInterface $pixel = null;

    protected ?ClientId $clientId = null;

    protected bool $consentGranted = false;

    protected ?string $eventName = null;

    protected array $data = [];

    protected string $state = self::STATE_PENDING;

    protected ?string $bulkIdentifier = null;

    protected ?DateTimeInterface $createdAt = null;

    protected ?DateTimeInterface $updatedAt = null;

    public function __construct()
    {
        $this->id = (string) Uuid::v4();
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getPixel(): ?PixelInterface
    {
        return $this->pixel;
    }

    public function setPixel(?PixelInterface $pixel): void
    {
        $this->pixel = $pixel;
    }

    public function getClientId(): ?ClientId
    {
        return $this->clientId;
    }

    public function setClientId(?ClientId $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function isConsentGranted(): bool
    {
        return $this->consentGranted;
    }

    public function setConsentGranted(bool $consentGranted): void
    {
        $this->consentGranted = $consentGranted;
    }

    public function getEventName(): ?string
    {
        return $this->eventName;
    }

    public function setEventName(?string $eventName): void
    {
        $this->eventName = $eventName;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function setData(array $data): void
    {
        $this->data = $data;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}
