<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Generator;

use Doctrine\ORM\EntityManagerInterface;
use Setono\MainRequestTrait\MainRequestTrait;
use Setono\SyliusFacebookPlugin\Context\PixelContextInterface;
use Setono\SyliusFacebookPlugin\DataMapper\DataMapperInterface;
use Setono\SyliusFacebookPlugin\Factory\PixelEventFactoryInterface;
use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class PixelEventsGenerator implements PixelEventsGeneratorInterface
{
    use MainRequestTrait;

    private PixelContextInterface $pixelContext;

    private RequestStack $requestStack;

    private ServerSideEventFactoryInterface $serverSideFactory;

    private DataMapperInterface $dataMapper;

    private PixelEventFactoryInterface $pixelEventFactory;

    private EntityManagerInterface $entityManager;

    public function __construct(
        PixelContextInterface $pixelContext,
        RequestStack $requestStack,
        ServerSideEventFactoryInterface $serverSideFactory,
        DataMapperInterface $dataMapper,
        PixelEventFactoryInterface $pixelEventFactory,
        EntityManagerInterface $entityManager
    ) {
        $this->pixelContext = $pixelContext;
        $this->requestStack = $requestStack;
        $this->serverSideFactory = $serverSideFactory;
        $this->dataMapper = $dataMapper;
        $this->pixelEventFactory = $pixelEventFactory;
        $this->entityManager = $entityManager;
    }

    /**
     * @param object $source
     */
    public function generatePixelEvents($source, string $eventName, Request $request = null): void
    {
        $serverSideEvent = $this->serverSideFactory->create($eventName);
        $this->dataMapper->map($source, $serverSideEvent, [
            'request' => $request ?? $this->getMainRequestFromRequestStack($this->requestStack),
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
