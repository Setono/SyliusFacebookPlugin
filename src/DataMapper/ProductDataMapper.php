<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\DataMapper;

use FacebookAds\Object\ServerSide\Content;
use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use Sylius\Component\Core\Model\ProductInterface;

/* not final */ class ProductDataMapper implements DataMapperInterface
{
    /**
     * @psalm-assert-if-true ProductInterface $source
     */
    public function supports($source, ServerSideEventInterface $target, array $context = []): bool
    {
        return $source instanceof ProductInterface;
    }

    /**
     * @param ProductInterface $source
     */
    public function map($source, ServerSideEventInterface $target, array $context = []): void
    {
        $customData = $target->getCustomData();
        $customData
            ->setContentName($source->getName())
            ->setContentType(ServerSideEventInterface::CONTENT_TYPE_PRODUCT)
            ->setContentIds($this->getContentIds($source))
            ->setContents($this->getContents($source))
        ;
    }

    protected function getContentIds(ProductInterface $product): array
    {
        return [
            $product->getCode(),
        ];
    }

    /**
     * @return array|Content[]
     */
    protected function getContents(ProductInterface $product): array
    {
        return [
            (new Content())
                ->setProductId($product->getCode())
                ->setQuantity(1),
        ];
    }
}
