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
use Setono\TagBagBundle\TagBag\TagBagInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\Context\CartContextInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class AddToCartSubscriber extends TagSubscriber
{
    /**
     * @var CartContextInterface
     */
    private $cartContext;

    public function __construct(
        TagBagInterface $tagBag,
        PixelContextInterface $pixelContext,
        EventDispatcherInterface $eventDispatcher,
        CartContextInterface $cartContext
    ) {
        parent::__construct($tagBag, $pixelContext, $eventDispatcher);

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
        if (!$this->hasPixels()) {
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

            $this->eventDispatcher->dispatch(ContentBuilder::EVENT_NAME, new BuilderEvent($contentBuilder, $item));

            $builder->addContent($contentBuilder);
        }

        $this->eventDispatcher->dispatch(AddToCartBuilder::EVENT_NAME, new BuilderEvent($builder, $order));

        $this->tagBag->add(new FbqTag(
            Tags::TAG_ADD_TO_CART,
            FbqTagInterface::EVENT_ADD_TO_CART,
            $builder
        ), TagBagInterface::SECTION_BODY_END);
    }
}
