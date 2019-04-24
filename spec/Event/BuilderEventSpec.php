<?php

declare(strict_types=1);

namespace spec\Setono\SyliusFacebookTrackingPlugin\Event;

use PhpSpec\ObjectBehavior;
use Setono\SyliusFacebookTrackingPlugin\Builder\BuilderInterface;
use Setono\SyliusFacebookTrackingPlugin\Event\BuilderEvent;

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
