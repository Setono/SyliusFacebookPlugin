<?php

declare(strict_types=1);

namespace spec\Setono\SyliusFacebookTrackingPlugin\EventListener;

use PhpSpec\ObjectBehavior;
use Setono\SyliusFacebookTrackingPlugin\EventListener\AddPaymentInfoEventListener;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class AddPaymentInfoEventListenerSpec extends ObjectBehavior
{
    function let(SessionInterface $session): void
    {
        $this->beConstructedWith($session);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(AddPaymentInfoEventListener::class);
    }

    function it_add_payment_info(SessionInterface $session): void
    {
        $session->has('facebook_events')->willReturn(true);

        $session->get('facebook_events')->shouldBeCalled();

        $session->set('facebook_events', [['name' => 'AddPaymentInfo']])->shouldBeCalled();

        $this->addPaymentInfo();
    }

    function it_cannot_add_payment_info(SessionInterface $session): void
    {
        $session->has('facebook_events')->willReturn(false);

        $session->set('facebook_events', [])->shouldBeCalled();

        $session->get('facebook_events')->shouldBeCalled();

        $session->set('facebook_events', [['name' => 'AddPaymentInfo']])->shouldBeCalled();

        $this->addPaymentInfo();
    }
}
