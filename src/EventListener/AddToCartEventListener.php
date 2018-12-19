<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\EventListener;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class AddToCartEventListener
{
    /** @var SessionInterface */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function addToCart(): void
    {
        if (!$this->session->has('facebook_events')) {
            $this->session->set('facebook_events', []);
        }

        $facebookEvents = $this->session->get('facebook_events');

        $facebookEvents[] = ['name' => 'AddToCart'];

        $this->session->set('facebook_events', $facebookEvents);
    }
}
