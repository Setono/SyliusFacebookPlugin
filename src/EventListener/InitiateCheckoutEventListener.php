<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\EventListener;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class InitiateCheckoutEventListener
{
    /** @var SessionInterface */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function initiateCheckout(): void
    {
        if (!$this->session->has('facebook_events')) {
            $this->session->set('facebook_events', []);
        }

        $facebookEvents = $this->session->get('facebook_events');

        $facebookEvents[] = ['name' => 'InitiateCheckout'];

        $this->session->set('facebook_events', $facebookEvents);
    }
}
