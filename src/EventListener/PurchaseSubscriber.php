<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusFacebookPlugin\Context\PixelContextInterface;
use Setono\SyliusFacebookPlugin\DataMapper\DataMapperInterface;
use Setono\SyliusFacebookPlugin\Factory\PixelEventFactoryInterface;
use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventFactoryInterface;
use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class PurchaseSubscriber extends AbstractSubscriber
{
    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        PixelContextInterface $pixelContext,
        RequestStack $requestStack,
        FirewallMap $firewallMap,
        ServerSideEventFactoryInterface $serverSideFactory,
        DataMapperInterface $dataMapper,
        PixelEventFactoryInterface $pixelEventFactory,
        EntityManagerInterface $entityManager,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct(
            $pixelContext,
            $requestStack,
            $firewallMap,
            $serverSideFactory,
            $dataMapper,
            $pixelEventFactory,
            $entityManager
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
        $order = $this->resolveOrder($requestEvent);
        if (null === $order) {
            return;
        }

        if (!$this->pixelContext->hasPixels()) {
            return;
        }

        $this->generatePixelEvents(
            $order,
            ServerSideEventInterface::EVENT_PURCHASE
        );
    }

    /**
     * This method will return an OrderInterface if
     * - We are on the 'thank you' page
     * - A session exists with the order id
     * - The order can be found in the order repository
     */
    private function resolveOrder(RequestEvent $requestEvent): ?OrderInterface
    {
        $request = $requestEvent->getRequest();

        if (!$requestEvent->isMasterRequest()) {
            return null;
        }

        if (!$request->attributes->has('_route')) {
            return null;
        }

        $route = $request->attributes->get('_route');
        if ('sylius_shop_order_thank_you' !== $route) {
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
