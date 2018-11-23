<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Context;

use Setono\SyliusFacebookTrackingPlugin\Entity\FacebookConfigInterface;
use Setono\SyliusFacebookTrackingPlugin\Repository\FacebookConfigRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

final class FacebookConfigContext implements FacebookConfigContextInterface
{
    /** @var FacebookConfigRepositoryInterface */
    private $facebookConfigRepository;

    /** @var FactoryInterface */
    private $facebookConfigFactory;

    public function __construct(
        FacebookConfigRepositoryInterface $facebookConfigRepository,
        FactoryInterface $facebookConfigFactory
    ) {
        $this->facebookConfigRepository = $facebookConfigRepository;
        $this->facebookConfigFactory = $facebookConfigFactory;
    }

    public function getConfig(): FacebookConfigInterface
    {
        $config = $this->facebookConfigRepository->findConfig();
        if (null === $config) {
            /** @var FacebookConfigInterface $config */
            $config = $this->facebookConfigFactory->createNew();
            $config->setPixelCode(self::DEFAULT_CODE);
            $this->facebookConfigRepository->add($config);
        }

        return $config;
    }
}
