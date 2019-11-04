<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Builder;

trait ValueAwareBuilderTrait
{
    /** @var array */
    protected $data = [];

    public function setCurrency(string $currency): self
    {
        $this->data['currency'] = $currency;

        return $this;
    }

    public function setValue(float $value): self
    {
        $this->data['value'] = $value;

        return $this;
    }
}
