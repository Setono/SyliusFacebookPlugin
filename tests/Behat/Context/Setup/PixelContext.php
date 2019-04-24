<?php

declare(strict_types=1);

namespace Tests\Setono\SyliusFacebookTrackingPlugin\Behat\Context\Setup;

use Behat\Behat\Context\Context;
use Setono\SyliusFacebookTrackingPlugin\Model\PixelInterface;
use Setono\SyliusFacebookTrackingPlugin\Repository\PixelRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class PixelContext implements Context
{
    /**
     * @var PixelRepositoryInterface
     */
    private $pixelRepository;

    /**
     * @var FactoryInterface
     */
    private $pixelFactory;

    public function __construct(PixelRepositoryInterface $pixelRepository, FactoryInterface $pixelFactory)
    {
        $this->pixelRepository = $pixelRepository;
        $this->pixelFactory = $pixelFactory;
    }

    /**
     * @Given the store has a pixel with pixel id :pixelId
     */
    public function theStoreHasAPropertyWithTrackingId($pixelId): void
    {
        $obj = $this->createProperty($pixelId);

        $this->save($obj);
    }

    private function createProperty(string $pixelId): PixelInterface
    {
        /** @var PixelInterface $obj */
        $obj = $this->pixelFactory->createNew();

        $obj->setPixelId($pixelId);

        return $obj;
    }

    private function save(PixelInterface $pixel): void
    {
        $this->pixelRepository->add($pixel);
    }
}
