<?php

namespace spec\Setono\SyliusFacebookTrackingPlugin\EventListener;

use Setono\SyliusFacebookTrackingPlugin\Context\FacebookConfigContextInterface;
use Setono\SyliusFacebookTrackingPlugin\EventListener\FacebookTrackingEventListener;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sonata\BlockBundle\Event\BlockEvent;
use Sonata\BlockBundle\Model\Block;

class FacebookTrackingEventListenerSpec extends ObjectBehavior
{
    function let(FacebookConfigContextInterface $facebookConfigContext): void
    {
        $this->beConstructedWith('template', $facebookConfigContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FacebookTrackingEventListener::class);
    }

    function itWorksOnBlockEvent(
        string $template,
        FacebookConfigContextInterface $facebookConfigContext,
        BlockEvent $event,
        Block $block
    ): void {
        $block->setId(uniqid('', true))->shouldBeCalled;
        $block
            ->setSettings(array_replace($event->getSettings(), [
                'template' => $template,
                'attr' => ['config' => $facebookConfigContext->getConfig()],
        ]))
        ->willReturn($block);
        ;
        $this->onBlockEvent($event);
    }
}
