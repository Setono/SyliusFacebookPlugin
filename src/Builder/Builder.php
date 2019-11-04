<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Builder;

use Safe\Exceptions\JsonException;
use function Safe\json_decode;
use function Safe\json_encode;

abstract class Builder implements BuilderInterface
{
    public const CONTENT_TYPE_PRODUCT = 'product';

    public const CONTENT_TYPE_PRODUCT_GROUP = 'product_group';

    /** @var array */
    protected $data = [];

    private function __construct()
    {
    }

    public static function create()
    {
        return new static();
    }

    /**
     * @throws JsonException
     */
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

    /**
     * @throws JsonException
     */
    public function getJson(): string
    {
        return json_encode($this->data);
    }
}
