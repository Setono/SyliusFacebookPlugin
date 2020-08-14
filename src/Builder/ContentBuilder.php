<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Builder;

use Webmozart\Assert\Assert;

final class ContentBuilder extends Builder
{
    /**
     * @param mixed $id
     */
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
