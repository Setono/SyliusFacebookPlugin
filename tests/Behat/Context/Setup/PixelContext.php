<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusFacebookPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Setono\SyliusFacebookPlugin\Model\PixelInterface;
use Setono\SyliusFacebookPlugin\Repository\PixelRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class PixelContext implements Context
{
    private PixelRepositoryInterface $pixelRepository;

    private FactoryInterface $pixelFactory;

    public function __construct(PixelRepositoryInterface $pixelRepository, FactoryInterface $pixelFactory)
    {
        $this->pixelRepository = $pixelRepository;
        $this->pixelFactory = $pixelFactory;
    }

    /**
     * @Given the store has a pixel with pixel id :pixelId
     */
    public function theStoreHasAPixelWithPixelId(string $pixelId): void
    {
        $pixel = $this->createPixel($pixelId);

        $this->save($pixel);
    }

    private function createPixel(string $pixelId): PixelInterface
    {
        /** @var PixelInterface $pixel */
        $pixel = $this->pixelFactory->createNew();

        $pixel->setPixelId($pixelId);

        return $pixel;
    }

    private function save(PixelInterface $pixel): void
    {
        $this->pixelRepository->add($pixel);
    }
}
