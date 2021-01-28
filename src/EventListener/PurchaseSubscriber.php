<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventListener;

use Psr\EventDispatcher\EventDispatcherInterface;
use Setono\SyliusFacebookPlugin\Builder\ContentBuilder;
use Setono\SyliusFacebookPlugin\Builder\PurchaseBuilder;
use Setono\SyliusFacebookPlugin\Context\PixelContextInterface;
use Setono\SyliusFacebookPlugin\Event\BuilderEvent;
use Setono\SyliusFacebookPlugin\Tag\FbqTag;
use Setono\SyliusFacebookPlugin\Tag\FbqTagInterface;
use Setono\SyliusFacebookPlugin\Tag\Tags;
use Setono\TagBag\Tag\TagInterface;
use Setono\TagBag\TagBagInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Repository\OrderRepositoryInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

final class PurchaseSubscriber extends TagSubscriber
{
    private OrderRepositoryInterface $orderRepository;

    public function __construct(
        TagBagInterface $tagBag,
        PixelContextInterface $pixelContext,
        EventDispatcherInterface $eventDispatcher,
        RequestStack $requestStack,
        FirewallMap $firewallMap,
        OrderRepositoryInterface $orderRepository
    ) {
        parent::__construct($tagBag, $pixelContext, $eventDispatcher, $requestStack, $firewallMap);

        $this->orderRepository = $orderRepository;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => 'track',
        ];
    }

    public function track(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if (!$event->isMasterRequest() || !$this->isShopContext($request)) {
            return;
        }

        if (!$request->attributes->has('_route')) {
            return;
        }

        $route = $request->attributes->get('_route');
        if ('sylius_shop_order_thank_you' !== $route) {
            return;
        }

        $orderId = $request->getSession()->get('sylius_order_id');

        if (null === $orderId) {
            return;
        }

        $order = $this->orderRepository->find($orderId);
        if (null === $order) {
            return;
        }

        if (!$order instanceof OrderInterface) {
            return;
        }

        if (!$this->hasPixels()) {
            return;
        }

        $builder = PurchaseBuilder::create()
            ->setValue($this->moneyFormatter->format($order->getTotal()))
            ->setCurrency($order->getCurrencyCode())
            ->setContentType(PurchaseBuilder::CONTENT_TYPE_PRODUCT)
        ;

        foreach ($order->getItems() as $orderItem) {
            $variant = $orderItem->getVariant();
            if (null === $variant) {
                continue;
            }

            $builder->addContentId($variant->getCode());

            $contentBuilder = ContentBuilder::create()
                ->setId($variant->getCode())
                ->setQuantity($orderItem->getQuantity())
            ;

            $this->eventDispatcher->dispatch(new BuilderEvent($contentBuilder, $orderItem));

            $builder->addContent($contentBuilder);
        }

        $this->eventDispatcher->dispatch(new BuilderEvent($builder, $order));

        $this->tagBag->addTag(
            (new FbqTag(FbqTagInterface::EVENT_PURCHASE, $builder))
                ->setSection(TagInterface::SECTION_BODY_END)
                ->setName(Tags::TAG_PURCHASE)
        );
    }
}
