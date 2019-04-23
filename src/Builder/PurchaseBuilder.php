<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Builder;

/**
 * todo implement 'contents' field https://developers.facebook.com/docs/facebook-pixel/reference/#object-properties
 *
 * @method PurchaseBuilder setValue(float $value)
 * @method PurchaseBuilder setCurrency(string $currency)
 */
final class PurchaseBuilder extends Builder implements ContentIdsAwareBuilderInterface, ContentsAwareBuilderInterface, ContentTypeAwareBuilderInterface
{
    use ContentIdsAwareBuilderTrait, ContentsAwareBuilderTrait, ContentTypeAwareBuilderTrait;

    public const EVENT_NAME = 'setono_sylius_facebook_tracking.builder.purchase';
}
