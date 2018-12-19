<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\EventListener;

use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class PurchaseEventListener
{
    /** @var SessionInterface */
    private $session;

    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function purchase(ResourceControllerEvent $resourceControllerEvent): void
    {
        /** @var OrderInterface $order */
        $order = $resourceControllerEvent->getSubject();

        if (!$this->session->has('facebook_events')) {
            $this->session->set('facebook_events', []);
        }

        $facebookEvents = $this->session->get('facebook_events');

        $facebookEvents[] = [
            'name' => 'Purchase',
            'options' => [
                'value' => $order->getTotal() / 100,
                'currency' => $order->getCurrencyCode(),
            ],
        ];

        $this->session->set('facebook_events', $facebookEvents);
    }
}
