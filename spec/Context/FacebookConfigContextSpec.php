<?php

declare(strict_types=1);

namespace spec\Setono\SyliusFacebookTrackingPlugin\Context;

use PhpSpec\ObjectBehavior;
use Setono\SyliusFacebookTrackingPlugin\Context\FacebookConfigContext;
use Setono\SyliusFacebookTrackingPlugin\Context\FacebookConfigContextInterface;
use Setono\SyliusFacebookTrackingPlugin\Entity\FacebookConfigInterface;
use Setono\SyliusFacebookTrackingPlugin\Repository\FacebookConfigRepositoryInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class FacebookConfigContextSpec extends ObjectBehavior
{
    function let(
        FacebookConfigRepositoryInterface $facebookConfigRepository,
        FactoryInterface $facebookConfigFactory
    ): void {
        $this->beConstructedWith($facebookConfigRepository, $facebookConfigFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FacebookConfigContext::class);
    }

    function it_implements_facebook_config_context_interface(): void
    {
        $this->shouldHaveType(FacebookConfigContextInterface::class);
    }

    function it_gets_config(
        FacebookConfigRepositoryInterface $facebookConfigRepository,
        FacebookConfigInterface $config
    ): void {
        $facebookConfigRepository->findConfig()->willReturn($config);

        $this->getConfig();
    }

    function it_creates_new_config_when_config_is_null(
        FacebookConfigRepositoryInterface $facebookConfigRepository,
        FactoryInterface $facebookConfigFactory,
        FacebookConfigInterface $config
    ): void {
        $facebookConfigRepository->findConfig()->willReturn(null);
        $facebookConfigFactory->createNew()->willReturn($config);

        $config->setPixelCode('default')->shouldBeCalled();
        $facebookConfigRepository->add($config)->shouldBeCalled();

        $this->getConfig();
    }
}
