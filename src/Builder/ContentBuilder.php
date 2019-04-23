<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Builder;

/**
 * @method ContentBuilder setId(string|int $id)
 * @method ContentBuilder setQuantity(int $quantity)
 * @method ContentBuilder setItemPrice(float $itemPrice)
 */
final class ContentBuilder extends Builder
{
    public const EVENT_NAME = 'setono_sylius_facebook_tracking.builder.item';
}
