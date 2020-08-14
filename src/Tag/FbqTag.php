<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Tag;

use Setono\SyliusFacebookPlugin\Builder\BuilderInterface;
use Setono\TagBag\Tag\TwigTag;

class FbqTag extends TwigTag implements FbqTagInterface
{
    /** @var string */
    private $event;

    /** @var BuilderInterface|null */
    private $parameters;

    public function __construct(string $event, BuilderInterface $builder = null)
    {
        parent::__construct('@SetonoSyliusFacebookPlugin/Tag/event.html.twig');

        $this->event = $event;
        $this->parameters = $builder;
    }

    public function getContext(): array
    {
        return $this->getParameters();
    }

    protected function getParameters(): array
    {
        $ret = ['event' => $this->event];

        if (null !== $this->parameters) {
            $ret['parameters'] = $this->parameters->getJson();
        }

        return $ret;
    }
}
