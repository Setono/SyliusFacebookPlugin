<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Tag;

use Setono\SyliusFacebookTrackingPlugin\Builder\BuilderInterface;

final class FbqTag implements FbqTagInterface
{
    /**
     * @var string
     */
    private $action;

    /**
     * @var string
     */
    private $key;

    /**
     * @var BuilderInterface|null
     */
    private $parameters;

    public function __construct(string $key, string $action, BuilderInterface $builder = null)
    {
        $this->key = $key;
        $this->action = $action;
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
        $ret = ['action' => $this->action];

        if (null !== $this->parameters) {
            $ret['parameters'] = $this->parameters->getJson();
        }

        return $ret;
    }
}
