<?php

declare(strict_types=1);

namespace spec\Setono\SyliusFacebookTrackingPlugin\EventListener;

use PhpSpec\ObjectBehavior;
use Setono\SyliusFacebookTrackingPlugin\EventListener\InitiateCheckoutEventListener;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class InitiateCheckoutEventListenerSpec extends ObjectBehavior
{
    function let(SessionInterface $session): void
    {
        $this->beConstructedWith($session);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(InitiateCheckoutEventListener::class);
    }

    function it_initiate_checkout(SessionInterface $session): void
    {
        $session->has('facebook_events')->willReturn(true);

        $session->get('facebook_events')->shouldBeCalled();

        $session->set('facebook_events', [['name' => 'InitiateCheckout']])->shouldBeCalled();

        $this->initiateCheckout();
    }

    function it_cannot_initiate_checkout(SessionInterface $session): void
    {
        $session->has('facebook_events')->willReturn(false);

        $session->set('facebook_events', [])->shouldBeCalled();

        $session->get('facebook_events')->shouldBeCalled();

        $session->set('facebook_events', [['name' => 'InitiateCheckout']])->shouldBeCalled();

        $this->initiateCheckout();
    }
}
