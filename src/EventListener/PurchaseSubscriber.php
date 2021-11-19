<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventListener;

use Setono\BotDetectionBundle\BotDetector\BotDetectorInterface;
use Setono\SyliusFacebookPlugin\Context\PixelContextInterface;
use Setono\SyliusFacebookPlugin\Generator\PixelEventsGeneratorInterface;
use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class PurchaseSubscriber extends AbstractSubscriber
{
    protected OrderRepositoryInterface $orderRepository;

    public function __construct(
        RequestStack $requestStack,
        FirewallMap $firewallMap,
        PixelContextInterface $pixelContext,
        PixelEventsGeneratorInterface $pixelEventsGenerator,
        BotDetectorInterface $botDetector,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct(
            $requestStack,
            $firewallMap,
            $pixelContext,
            $pixelEventsGenerator,
            $botDetector
        );

        $this->orderRepository = $orderRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'track',
        ];
    }

    public function track(RequestEvent $requestEvent): void
    {
        if (!$this->isRequestEligible() || !$this->pixelContext->hasPixels()) {
            return;
        }

        $order = $this->resolveOrder();
        if (null === $order) {
            return;
        }

        $this->pixelEventsGenerator->generatePixelEvents(
            $order,
            ServerSideEventInterface::EVENT_PURCHASE
        );
    }

    /**
     * Request is eligible when:
     * - We are on the 'thank you' page
     */
    protected function isRequestEligible(): bool
    {
        if (!parent::isRequestEligible()) {
            return false;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return false;
        }

        return 'sylius_shop_order_thank_you' === $request->attributes->get('_route');
    }

    /**
     * This method will return an OrderInterface if
     * - A session exists with the order id
     * - The order can be found in the order repository
     */
    private function resolveOrder(): ?OrderInterface
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return null;
        }

        /** @var mixed $orderId */
        $orderId = $request->getSession()->get('sylius_order_id');

        if (!is_scalar($orderId)) {
            return null;
        }

        $order = $this->orderRepository->find($orderId);
        if (!$order instanceof OrderInterface) {
            return null;
        }

        return $order;
    }
}
