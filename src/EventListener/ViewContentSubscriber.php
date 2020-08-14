<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\EventListener;

use Setono\SyliusFacebookTrackingPlugin\Builder\ContentBuilder;
use Setono\SyliusFacebookTrackingPlugin\Builder\ViewContentBuilder;
use Setono\SyliusFacebookTrackingPlugin\Event\BuilderEvent;
use Setono\SyliusFacebookTrackingPlugin\Tag\FbqTag;
use Setono\SyliusFacebookTrackingPlugin\Tag\FbqTagInterface;
use Setono\SyliusFacebookTrackingPlugin\Tag\Tags;
use Setono\TagBag\Tag\TagInterface;
use Sylius\Bundle\ResourceBundle\Event\ResourceControllerEvent;
use Sylius\Component\Product\Model\ProductInterface;

final class ViewContentSubscriber extends TagSubscriber
{
    public static function getSubscribedEvents(): array
    {
        return [
            'sylius.product.show' => [
                'track',
            ],
        ];
    }

    public function track(ResourceControllerEvent $event): void
    {
        if (!$this->isShopContext() || !$this->hasPixels()) {
            return;
        }

        $product = $event->getSubject();
        if (!$product instanceof ProductInterface) {
            return;
        }

        $builder = ViewContentBuilder::create()
            ->setContentName($product->getName())
            ->setContentType(ViewContentBuilder::CONTENT_TYPE_PRODUCT)
            ->addContentId($product->getCode())
        ;

        $contentBuilder = ContentBuilder::create()
            ->setId($product->getCode())
            ->setQuantity(1)
        ;

        $this->eventDispatcher->dispatch(new BuilderEvent($contentBuilder, $product));

        $builder->addContent($contentBuilder);

        $this->eventDispatcher->dispatch(new BuilderEvent($builder, $product));

        $this->tagBag->addTag(
            (new FbqTag(FbqTagInterface::EVENT_VIEW_CONTENT, $builder))
                ->setSection(TagInterface::SECTION_BODY_END)
                ->setName(Tags::TAG_VIEW_CONTENT)
        );
    }
}
