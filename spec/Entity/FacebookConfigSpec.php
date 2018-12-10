<?php

declare(strict_types=1);

namespace spec\Setono\SyliusFacebookTrackingPlugin\Entity;

use PhpSpec\ObjectBehavior;
use Setono\SyliusFacebookTrackingPlugin\Entity\FacebookConfig;
use Setono\SyliusFacebookTrackingPlugin\Entity\FacebookConfigInterface;

class FacebookConfigSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(FacebookConfig::class);
    }

    function it_implements_facebook_config_interface(): void
    {
        $this->shouldHaveType(FacebookConfigInterface::class);
    }

    function it_has_null_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_pixelcode_is_mutable(): void
    {
        $this->setPixelCode('code1');
        $this->getPixelCode()->shouldReturn('code1');
    }

    function it_toggles(): void
    {
        $this->enable();
        $this->isEnabled()->shouldReturn(true);
        $this->disable();
        $this->isEnabled()->shouldReturn(false);
    }
}
