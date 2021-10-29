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
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\RequestStack;

final class AddToCartSubscriber extends AbstractSubscriber
{
    private CartContextInterface $cartContext;

    public function __construct(
        PixelContextInterface $pixelContext,
        RequestStack $requestStack,
        FirewallMap $firewallMap,
        ServerSideEventFactoryInterface $serverSideFactory,
        DataMapperInterface $dataMapper,
        PixelEventFactoryInterface $pixelEventFactory,
        EntityManagerInterface $entityManager,
        CartContextInterface $cartContext
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

        $this->cartContext = $cartContext;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.order_item.post_add' => [
                'track',
            ],
        ];
    }

    public function track(): void
    {
        if (!$this->isShopContext() || !$this->pixelContext->hasPixels()) {
            return;
        }

        $order = $this->cartContext->getCart();
        if (!$order instanceof OrderInterface) {
            return;
        }

        $this->generatePixelEvents(
            $order,
            ServerSideEventInterface::EVENT_ADD_TO_CART
        );
    }
}
