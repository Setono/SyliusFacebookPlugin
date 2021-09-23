<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Event;

use Setono\SyliusFacebookPlugin\Builder\BuilderInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\EventDispatcher\Event as ContractsEvent;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface as ContractsEventDispatcherInterface;

if (is_subclass_of(EventDispatcherInterface::class, ContractsEventDispatcherInterface::class)) {
    abstract class BCEvent extends ContractsEvent
    {
    }
} else {
    abstract class BCEvent extends Event
    {
    }
}

final class BuilderEvent extends BCEvent
{
    private BuilderInterface $builder;

    /** @var mixed|null */
    private $subject;

    /**
     * @param mixed|null $subject
     */
    public function __construct(BuilderInterface $builder, $subject = null)
    {
        $this->builder = $builder;
        $this->subject = $subject;
    }

    public function getBuilder(): BuilderInterface
    {
        return $this->builder;
    }

    /**
     * @return mixed|null
     */
    public function getSubject()
    {
        return $this->subject;
    }
}
