<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\EventListener;

use Setono\SyliusFacebookTrackingPlugin\Context\FacebookConfigContextInterface;
use Sonata\BlockBundle\Event\BlockEvent;
use Sonata\BlockBundle\Model\Block;

final class FacebookTrackingEventListener
{
    /**
     * @var string
     */
    private $template;

    /** @var FacebookConfigContextInterface */
    private $facebookConfigContext;

    public function __construct(string $template, FacebookConfigContextInterface $facebookConfigContext)
    {
        $this->template = $template;
        $this->facebookConfigContext = $facebookConfigContext;
    }

    /**
     * @param BlockEvent $event
     */
    public function onBlockEvent(BlockEvent $event): void
    {
        $block = new Block();
        $block->setId(uniqid('', true));
        $block->setSettings(array_replace($event->getSettings(), [
            'template' => $this->template,
            'attr' => [
                'config' => $this->facebookConfigContext->getConfig(),
            ],
        ]));
        $block->setType('sonata.block.service.template');

        $event->addBlock($block);
    }
}
