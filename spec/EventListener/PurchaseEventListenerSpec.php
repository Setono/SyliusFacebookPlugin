<?php

declare(strict_types=1);

namespace spec\Setono\SyliusFacebookTrackingPlugin\EventListener;

use PhpSpec\ObjectBehavior;
use Setono\SyliusFacebookTrackingPlugin\EventListener\PurchaseEventListener;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class PurchaseEventListenerSpec extends ObjectBehavior
{
    function let(SessionInterface $session): void
    {
        $this->beConstructedWith($session);
    }

    function it_is_initializable(): void
    {
        $this->shouldHaveType(PurchaseEventListener::class);
    }

    function it_purchase(
        ResourceControllerEvent $resourceControllerEvent,
        SessionInterface $session,
        OrderInterface $order
    ): void {
        $session->has('facebook_events')->willReturn(true);

        $session->get('facebook_events')->shouldBeCalled();

        $resourceControllerEvent->getSubject()->willReturn($order);

        $order->getTotal()->willReturn(100);
        $order->getCurrencyCode()->willReturn('USD');

        $session->set('facebook_events', [[
            'name' => 'Purchase',
            'options' => [
                'value' => 1,
                'currency' => 'USD',
            ],
        ]])->shouldBeCalled();

        $this->purchase($resourceControllerEvent);
    }

    function it_cannot_purchase(
        ResourceControllerEvent $resourceControllerEvent,
        SessionInterface $session,
        OrderInterface $order
    ): void {
        $session->has('facebook_events')->willReturn(false);

        $session->set('facebook_events', [])->shouldBeCalled();

        $session->get('facebook_events')->shouldBeCalled();

        $resourceControllerEvent->getSubject()->willReturn($order);

        $order->getTotal()->willReturn(100);
        $order->getCurrencyCode()->willReturn('USD');

        $session->set('facebook_events', [[
            'name' => 'Purchase',
            'options' => [
                'value' => 1,
                'currency' => 'USD',
            ],
        ]])->shouldBeCalled();

        $this->purchase($resourceControllerEvent);
    }
}
