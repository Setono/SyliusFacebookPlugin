<?php

declare(strict_types=1);

namespace spec\Setono\SyliusFacebookTrackingPlugin\EventListener;

use PhpSpec\ObjectBehavior;
use Setono\SyliusFacebookTrackingPlugin\EventListener\AddToCartEventListener;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AddToCartEventListenerSpec extends ObjectBehavior
{
    function let(SessionInterface $session): void
    {
        $this->beConstructedWith($session);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(AddToCartEventListener::class);
    }

    function it_add_to_cart(SessionInterface $session): void
    {
        $session->has('facebook_events')->willReturn(true);

        $session->get('facebook_events')->shouldBeCalled();

        $session->set('facebook_events', [['name' => 'AddToCart']])->shouldBeCalled();

        $this->addToCart();
    }

    function it_cannot_add_to_cart(SessionInterface $session): void
    {
        $session->has('facebook_events')->willReturn(false);

        $session->set('facebook_events', [])->shouldBeCalled();

        $session->get('facebook_events')->shouldBeCalled();

        $session->set('facebook_events', [['name' => 'AddToCart']])->shouldBeCalled();

        $this->addToCart();
    }
}
