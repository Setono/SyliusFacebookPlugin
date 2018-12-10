<?php

namespace spec\Setono\SyliusFacebookTrackingPlugin\Entity;

use Setono\SyliusFacebookTrackingPlugin\Entity\FacebookConfigTranslation;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Setono\SyliusFacebookTrackingPlugin\Entity\FacebookConfigTranslationInterface;

class FacebookConfigTranslationSpec extends ObjectBehavior
{
    function it_is_initializable(): void
    {
        $this->shouldHaveType(FacebookConfigTranslation::class);
    }

    function it_implements_facebook_config_translation_interface(): void
    {
        $this->shouldHaveType(FacebookConfigTranslationInterface::class);
    }

    function it_has_null_id_by_default(): void
    {
        $this->getId()->shouldReturn(null);
    }

    function it_name_is_mutable(): void
    {
        $this->setName('facebook');
        $this->getName()->shouldReturn('facebook');
    }
}
