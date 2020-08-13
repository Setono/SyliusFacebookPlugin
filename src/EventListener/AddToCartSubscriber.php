<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\EventListener;

use Setono\SyliusFacebookTrackingPlugin\Builder\AddToCartBuilder;
use Setono\SyliusFacebookTrackingPlugin\Builder\ContentBuilder;
use Setono\SyliusFacebookTrackingPlugin\Context\PixelContextInterface;
use Setono\SyliusFacebookTrackingPlugin\Event\BuilderEvent;
use Setono\SyliusFacebookTrackingPlugin\Tag\FbqTag;
use Setono\SyliusFacebookTrackingPlugin\Tag\FbqTagInterface;
use Setono\SyliusFacebookTrackingPlugin\Tag\Tags;
use Setono\TagBag\Tag\TagInterface;
use Setono\TagBag\TagBagInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class AddToCartSubscriber extends TagSubscriber
{
    /** @var CartContextInterface */
    private $cartContext;

    public function __construct(
        TagBagInterface $tagBag,
        PixelContextInterface $pixelContext,
        EventDispatcherInterface $eventDispatcher,
        CartContextInterface $cartContext,
        RequestStack $requestStack,
        FirewallMap $firewallMap
    ) {
        parent::__construct($tagBag, $pixelContext, $eventDispatcher, $requestStack, $firewallMap);

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
        if (!$this->isShopContext() || !$this->hasPixels()) {
            return;
        }

        $order = $this->cartContext->getCart();

        if (!$order instanceof OrderInterface) {
            return;
        }

        $builder = AddToCartBuilder::create()
            ->setCurrency($order->getCurrencyCode())
            ->setValue($this->moneyFormatter->format($order->getTotal()))
            ->setContentType(AddToCartBuilder::CONTENT_TYPE_PRODUCT)
        ;

        foreach ($order->getItems() as $item) {
            $variant = $item->getVariant();
            if (null === $variant) {
                continue;
            }

            $builder->addContentId($variant->getCode());

            $contentBuilder = ContentBuilder::create()
                ->setId($variant->getCode())
                ->setQuantity($item->getQuantity())
                ->setItemPrice($this->moneyFormatter->format($item->getDiscountedUnitPrice()))
            ;

            $this->eventDispatcher->dispatch(new BuilderEvent($contentBuilder, $item));

            $builder->addContent($contentBuilder);
        }

        $this->eventDispatcher->dispatch(new BuilderEvent($builder, $order));

        $this->tagBag->addTag(
            (new FbqTag(FbqTagInterface::EVENT_ADD_TO_CART, $builder))
                ->setSection(TagInterface::SECTION_BODY_END)
                ->setName(Tags::TAG_ADD_TO_CART)
        );
    }
}
