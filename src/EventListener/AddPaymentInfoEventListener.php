<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\EventListener;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class AddPaymentInfoEventListener
{
    /** @var SessionInterface */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function addPaymentInfo(): void
    {
        if (!$this->session->has('facebook_events')) {
            $this->session->set('facebook_events', []);
        }

        $facebookEvents = $this->session->get('facebook_events');

        $facebookEvents[] = ['name' => 'AddPaymentInfo'];

        $this->session->set('facebook_events', $facebookEvents);
    }
}
