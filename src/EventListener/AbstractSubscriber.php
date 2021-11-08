<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusFacebookPlugin\Context\PixelContextInterface;
use Setono\SyliusFacebookPlugin\DataMapper\DataMapperInterface;
use Setono\SyliusFacebookPlugin\Factory\PixelEventFactoryInterface;
use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventFactoryInterface;
use Symfony\Bundle\SecurityBundle\Security\FirewallMap;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractSubscriber implements EventSubscriberInterface
{
    protected PixelContextInterface $pixelContext;

    protected RequestStack $requestStack;

    protected FirewallMap $firewallMap;

    protected ServerSideEventFactoryInterface $serverSideFactory;

    protected DataMapperInterface $dataMapper;

    protected PixelEventFactoryInterface $pixelEventFactory;

    protected EntityManagerInterface $entityManager;

    public function __construct(
        PixelContextInterface $pixelContext,
        RequestStack $requestStack,
        FirewallMap $firewallMap,
        ServerSideEventFactoryInterface $serverSideFactory,
        DataMapperInterface $dataMapper,
        PixelEventFactoryInterface $pixelEventFactory,
        EntityManagerInterface $entityManager
    ) {
        $this->pixelContext = $pixelContext;
        $this->requestStack = $requestStack;
        $this->firewallMap = $firewallMap;
        $this->serverSideFactory = $serverSideFactory;
        $this->dataMapper = $dataMapper;
        $this->pixelEventFactory = $pixelEventFactory;
        $this->entityManager = $entityManager;
    }

    protected function getMasterRequest(): ?Request
    {
        return $this->requestStack->getMasterRequest();
    }

    protected function isShopContext(Request $request = null): bool
    {
        if (null === $request) {
            $request = $this->requestStack->getCurrentRequest();
            if (null === $request) {
                return true;
            }
        }

        $firewallConfig = $this->firewallMap->getFirewallConfig($request);
        if (null === $firewallConfig) {
            return true;
        }

        return $firewallConfig->getName() === 'shop';
    }

    /**
     * @param object $source
     */
    protected function generatePixelEvents($source, string $eventName, Request $request = null): void
    {
        $serverSideEvent = $this->serverSideFactory->create($eventName);
        $this->dataMapper->map($source, $serverSideEvent, [
            'request' => $request ?? $this->getMasterRequest(),
            'event' => $eventName,
        ]);

        $pixels = $this->pixelContext->getPixels();
        foreach ($pixels as $pixel) {
            // @todo Maybe its better to just clone
            $pixelEvent = $this->pixelEventFactory->createFromServerSideEvent($serverSideEvent);
            $pixelEvent->setPixel($pixel);

            $this->entityManager->persist($pixelEvent);
        }

        $this->entityManager->flush();
    }
}
