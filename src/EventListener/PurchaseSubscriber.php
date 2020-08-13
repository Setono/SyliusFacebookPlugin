<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\EventListener;

use Setono\SyliusFacebookTrackingPlugin\Builder\ContentBuilder;
use Setono\SyliusFacebookTrackingPlugin\Builder\PurchaseBuilder;
use Setono\SyliusFacebookTrackingPlugin\Event\BuilderEvent;
use Setono\SyliusFacebookTrackingPlugin\Tag\FbqTag;
use Setono\SyliusFacebookTrackingPlugin\Tag\FbqTagInterface;
use Setono\SyliusFacebookTrackingPlugin\Tag\Tags;
use Setono\TagBag\Tag\TagInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Core\Model\OrderInterface;

final class PurchaseSubscriber extends TagSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.order.post_complete' => [
                'track',
            ],
        ];
    }

    public function track(ResourceControllerEvent $event): void
    {
        $order = $event->getSubject();

        if (!$order instanceof OrderInterface || !$this->isShopContext()) {
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
