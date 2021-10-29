<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Factory;

use Setono\ClientId\Provider\ClientIdProviderInterface;
use Setono\Consent\Context\ConsentContextInterface;
use Setono\SyliusFacebookPlugin\Model\PixelEventInterface;
use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Symfony\Component\Serializer\SerializerInterface;

final class PixelEventFactory implements PixelEventFactoryInterface
{
    private FactoryInterface $decorated;

    private SerializerInterface $serializer;

    private ClientIdProviderInterface $clientIdProvider;

    private ConsentContextInterface $consentContext;

    public function __construct(
        FactoryInterface $decorated,
        SerializerInterface $serializer,
        ClientIdProviderInterface $clientIdProvider,
        ConsentContextInterface $consentContext
    ) {
        $this->decorated = $decorated;
        $this->serializer = $serializer;
        $this->clientIdProvider = $clientIdProvider;
        $this->consentContext = $consentContext;
    }

    public function createNew()
    {
        return $this->decorated->createNew();
    }

    public function createFromServerSideEvent(ServerSideEventInterface $event): PixelEventInterface
    {
        /** @var PixelEventInterface $pixelEvent */
        $pixelEvent = $this->createNew();
        $pixelEvent->setClientId(
            $this->clientIdProvider->getClientId()
        );
        $pixelEvent->setConsentGranted(
            $this->consentContext->getConsent()->isStatisticsConsentGranted()
        );
        $pixelEvent->setData($event->normalize());

        return $pixelEvent;
    }
}
