<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Builder;

use function Safe\json_decode;
use function Safe\json_encode;

abstract class Builder implements BuilderInterface
{
    /** @var array */
    protected $data = [];

    public static function create()
    {
        return new static();
    }

    public static function createFromJson(string $json)
    {
        $new = new static();
        $new->data = json_decode($json, true);

        return $new;
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getJson(): string
    {
        return json_encode($this->data);
    }
}
