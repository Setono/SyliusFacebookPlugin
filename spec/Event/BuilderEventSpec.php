<?php

declare(strict_types=1);

namespace spec\Setono\SyliusFacebookPlugin\Event;

use PhpSpec\ObjectBehavior;
use Setono\SyliusFacebookPlugin\Builder\BuilderInterface;
use Setono\SyliusFacebookPlugin\Event\BuilderEvent;

class BuilderEventSpec extends ObjectBehavior
{
    public function let(BuilderInterface $builder): void
    {
        $this->beConstructedWith($builder, 'subject');
    }

    public function it_is_initializable(): void
    {
        $this->shouldHaveType(BuilderEvent::class);
    }

    public function it_returns_correct_values(BuilderInterface $builder): void
    {
        $this->getBuilder()->shouldReturn($builder);
        $this->getSubject()->shouldReturn('subject');
    }
}
