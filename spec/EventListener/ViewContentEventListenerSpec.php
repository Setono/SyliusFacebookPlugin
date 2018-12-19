<?php

declare(strict_types=1);

namespace spec\Setono\SyliusFacebookTrackingPlugin\EventListener;

use PhpSpec\ObjectBehavior;
use Setono\SyliusFacebookTrackingPlugin\EventListener\ViewContentEventListener;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ViewContentEventListenerSpec extends ObjectBehavior
{
    function let(SessionInterface $session): void
    {
        $this->beConstructedWith($session);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ViewContentEventListener::class);
    }

    function it_view_content(SessionInterface $session): void
    {
        $session->has('facebook_events')->willReturn(true);

        $session->get('facebook_events')->shouldBeCalled();

        $session->set('facebook_events', [['name' => 'ViewContent']])->shouldBeCalled();

        $this->viewContent();
    }

    function it_cannot_view_content(SessionInterface $session): void
    {
        $session->has('facebook_events')->willReturn(false);

        $session->set('facebook_events', [])->shouldBeCalled();

        $session->get('facebook_events')->shouldBeCalled();

        $session->set('facebook_events', [['name' => 'ViewContent']])->shouldBeCalled();

        $this->viewContent();
    }
}
