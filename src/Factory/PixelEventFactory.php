<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Factory;

use Setono\ClientId\Provider\ClientIdProviderInterface;
use Setono\Consent\Context\ConsentContextInterface;
use Setono\SyliusFacebookPlugin\Model\PixelEventInterface;
use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;
use Webmozart\Assert\Assert;

final class PixelEventFactory implements PixelEventFactoryInterface
{
    private FactoryInterface $decorated;

    private ClientIdProviderInterface $clientIdProvider;

    private ConsentContextInterface $consentContext;

    public function __construct(
        FactoryInterface $decorated,
        ClientIdProviderInterface $clientIdProvider,
        ConsentContextInterface $consentContext
    ) {
        $this->decorated = $decorated;
        $this->clientIdProvider = $clientIdProvider;
        $this->consentContext = $consentContext;
    }

    public function createNew(): PixelEventInterface
    {
        $pixelEvent = $this->decorated->createNew();

        Assert::isInstanceOf($pixelEvent, PixelEventInterface::class);

        return $pixelEvent;
    }

    public function createFromServerSideEvent(ServerSideEventInterface $event): PixelEventInterface
    {
        $pixelEvent = $this->createNew();
        $pixelEvent->setEventName($event->getEventName());
        $pixelEvent->setClientId(
            $this->clientIdProvider->getClientId()
        );
        $pixelEvent->setConsentGranted(
            $this->consentContext->getConsent()->isMarketingConsentGranted()
        );
        $pixelEvent->setData($event->normalize());

        return $pixelEvent;
    }
}
