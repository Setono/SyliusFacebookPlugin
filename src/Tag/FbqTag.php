<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tag;

use Setono\SyliusFacebookPlugin\Builder\BuilderInterface;
use Setono\TagBag\Tag\TwigTag;

class FbqTag extends TwigTag implements FbqTagInterface
{
    private string $method;

    private string $event;

    private ?BuilderInterface $parameters = null;

    public function __construct(string $event, BuilderInterface $builder = null, string $method = 'track')
    {
        parent::__construct('@SetonoSyliusFacebookPlugin/Tag/event.html.twig');

        $this->method = $method;
        $this->event = $event;
        $this->parameters = $builder;
    }

    public function getContext(): array
    {
        return $this->getParameters();
    }

    protected function getParameters(): array
    {
        $ret = ['method' => $this->method, 'event' => $this->event];

        if (null !== $this->parameters) {
            $ret['parameters'] = $this->parameters->getJson();
        }

        return $ret;
    }
}
