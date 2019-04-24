<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Builder;

use Webmozart\Assert\Assert;

final class ContentBuilder extends Builder
{
    public const EVENT_NAME = 'setono_sylius_facebook_tracking.builder.item';

    public function setId($id): self
    {
        Assert::scalar($id);

        $this->data['id'] = $id;

        return $this;
    }

    public function setQuantity(int $quantity): self
    {
        $this->data['quantity'] = $quantity;

        return $this;
    }

    public function setItemPrice(float $itemPrice): self
    {
        $this->data['item_price'] = $itemPrice;

        return $this;
    }
}
