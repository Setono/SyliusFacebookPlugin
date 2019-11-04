<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Tag;

use Setono\SyliusFacebookTrackingPlugin\Builder\BuilderInterface;

final class FbqTag implements FbqTagInterface
{
    /** @var string */
    private $event;

    /** @var string */
    private $key;

    /** @var BuilderInterface|null */
    private $parameters;

    public function __construct(string $key, string $event, BuilderInterface $builder = null)
    {
        $this->key = $key;
        $this->event = $event;
        $this->parameters = $builder;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function getType(): string
    {
        return self::TYPE_SCRIPT;
    }

    public function getTemplate(): string
    {
        return '@SetonoSyliusFacebookTrackingPlugin/Tag/event.js.twig';
    }

    public function getParameters(): array
    {
        $ret = ['event' => $this->event];

        if (null !== $this->parameters) {
            $ret['parameters'] = $this->parameters->getJson();
        }

        return $ret;
    }
}
