<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\EventListener;

use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class ViewContentEventListener
{
    /** @var SessionInterface */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function viewContent(): void
    {
        if (!$this->session->has('facebook_events')) {
            $this->session->set('facebook_events', []);
        }

        $facebookEvents = $this->session->get('facebook_events');

        $facebookEvents[] = ['name' => 'ViewContent'];

        $this->session->set('facebook_events', $facebookEvents);
    }
}
