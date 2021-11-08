<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\DataMapper;

use FacebookAds\Object\ServerSide\Content;
use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use Sylius\Component\Product\Model\ProductInterface;
use Webmozart\Assert\Assert;

/* not final */ class ProductDataMapper implements DataMapperInterface
{
    /**
     * @psalm-assert-if-true ProductInterface $source
     */
    public function supports(object $source, ServerSideEventInterface $target, array $context = []): bool
    {
        return $source instanceof ProductInterface;
    }

    public function map(object $source, ServerSideEventInterface $target, array $context = []): void
    {
        Assert::true($this->supports($source, $target, $context));

        $customData = $target->getCustomData();

        /** @psalm-suppress PossiblyNullArgument */
        $customData->setContentName($source->getName());

        $customData->setContentType(ServerSideEventInterface::CONTENT_TYPE_PRODUCT);
        $customData->setContentIds($this->getContentIds($source));
        $customData->setContents($this->getContents($source));
    }

    /**
     * @return array<array-key, string>
     */
    protected function getContentIds(ProductInterface $product): array
    {
        /** @var array<array-key, string> $contentIds */
        $contentIds = array_filter([
            $product->getCode(),
        ]);

        return $contentIds;
    }

    /**
     * @return array<array-key, Content>
     */
    protected function getContents(ProductInterface $product): array
    {
        /** @var array<array-key, string> $contentIds */
        $contentIds = array_filter([
            $product->getCode(),
        ]);

        return array_map(function (string $contentId) {
            $content = new Content();
            $content->setProductId($contentId);
            $content->setQuantity(1);

            return $content;
        }, $contentIds);
    }
}
