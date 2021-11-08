<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Model;

use DateTimeInterface;
use Setono\ClientId\ClientId;
use Sylius\Component\Resource\Model\ResourceInterface;

interface PixelEventInterface extends ResourceInterface
{
    public const STATE_PENDING = 'pending';

    public const STATE_SENT = 'sent';

    public const STATE_FAILED = 'failed';

    public function getId(): string;

    public function getPixel(): ?PixelInterface;

    public function setPixel(?PixelInterface $pixel): void;

    public function getClientId(): ?ClientId;

    public function setClientId(?ClientId $clientId): void;

    public function isConsentGranted(): bool;

    public function setConsentGranted(bool $consentGranted): void;

    public function getEventName(): ?string;

    public function setEventName(?string $eventName): void;

    public function getData(): array;

    public function setData(array $data): void;

    public function getState(): string;

    public function setState(string $state): void;

    public function getCreatedAt(): ?DateTimeInterface;

    public function setCreatedAt(?DateTimeInterface $createdAt): void;

    public function getUpdatedAt(): ?DateTimeInterface;

    public function setUpdatedAt(DateTimeInterface $updatedAt): void;
}
