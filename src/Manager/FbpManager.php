<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Manager;

use Setono\ClientId\Provider\ClientIdProviderInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * We need this manager to be able to access fbp
 * at different stages of Request and generate it only once
 */
final class FbpManager implements FbpManagerInterface
{
    private RequestStack $requestStack;

    private ClientIdProviderInterface $clientIdProvider;

    private int $fbpTtl;

    private string $fbpCookieName;

    public function __construct(
        RequestStack $requestStack,
        ClientIdProviderInterface $clientIdProvider,
        int $fbpTtl,
        string $fbpCookieName = 'ssf_fbp'
    ) {
        $this->requestStack = $requestStack;
        $this->clientIdProvider = $clientIdProvider;
        $this->fbpTtl = $fbpTtl;
        $this->fbpCookieName = $fbpCookieName;
    }

    public function getfbpCookie(): ?Cookie
    {
        $fbp = $this->getfbpValue();
        if (null === $fbp) {
            return null;
        }

        return Cookie::create(
            $this->fbpCookieName,
            $fbp,
            time() + $this->fbpTtl
        );
    }

    /**
     * We call it twice per request:
     * 1. When populate fbp to UserData (on request)
     * 2. When setting a fbp cookie (on response)
     */
    public function getFbpValue(): ?string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return null;
        }

        /** @var string|null $fbp */
        $fbp = $request->cookies->get($this->fbpCookieName);

        // We have fbp cookie and shouldn't try to
        // regenerate it from fbplid (as it is empty)
        if (is_string($fbp)) {
            return $fbp;
        }

        $clientId = (string) $this->clientIdProvider->getClientId();

        return $this->generateFbp($clientId);
    }

    private function generateFbp(string $clientId): string
    {
        $creationTime = ceil(microtime(true) * 1000);

        return sprintf(
            'fb.1.%s.%s',
            $creationTime,
            $clientId
        );
    }
}
