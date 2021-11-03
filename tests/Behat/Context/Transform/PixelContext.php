<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusFacebookPlugin\Behat\Context\Transform;

use Behat\Behat\Context\Context;
use Setono\SyliusFacebookPlugin\Model\PixelInterface;
use Setono\SyliusFacebookPlugin\Repository\PixelRepositoryInterface;

final class PixelContext implements Context
{
    private PixelRepositoryInterface $pixelRepository;

    public function __construct(PixelRepositoryInterface $pixelRepository)
    {
        $this->pixelRepository = $pixelRepository;
    }

    /**
     * @Transform :pixel
     */
    public function getPixelByPixelId(string $pixel): ?PixelInterface
    {
        return $this->pixelRepository->findOneBy([
            'pixelId' => $pixel,
        ]);
    }
}
