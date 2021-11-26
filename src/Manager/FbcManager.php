<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Manager;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;

/**
 * We need this manager to be able to access fbc
 * at different stages of Request and generate it only once
 */
final class FbcManager implements FbcManagerInterface
{
    private RequestStack $requestStack;

    private int $fbcTtl;

    private string $fbcCookieName;

    /**
     * Cached value of fbc generated from fbclid at current request
     */
    private ?string $generatedFbc = null;

    public function __construct(
        RequestStack $requestStack,
        int $fbcTtl,
        string $fbcCookieName = 'ssf_fbc'
    ) {
        $this->requestStack = $requestStack;
        $this->fbcTtl = $fbcTtl;
        $this->fbcCookieName = $fbcCookieName;
    }

    public function getFbcCookie(): ?Cookie
    {
        $fbc = $this->getFbcValue();
        if (null === $fbc) {
            return null;
        }

        return Cookie::create(
            $this->fbcCookieName,
            $fbc,
            time() + $this->fbcTtl
        );
    }

    /**
     * We call it twice per request:
     * 1. When populate fbc to UserData (on request)
     * 2. When setting a fbc cookie (on response)
     */
    public function getFbcValue(): ?string
    {
        // We already have fbc generated at previous call
        if (null !== $this->generatedFbc) {
            return $this->generatedFbc;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return null;
        }

        /** @var string|null $fbc */
        $fbc = $request->cookies->get($this->fbcCookieName);

        /** @var string|null $fbclid */
        $fbclid = $request->query->get('fbclid');

        // We have both fbc and fbclid
        if (is_string($fbclid) && is_string($fbc)) {
            // So should decide if we should regenerate it.
            // Extracting fbclid from fbc to compare
            $existingFbclid = $this->extractFbclid($fbc);

            // If fbclid is the same - we shouldn't regenerate fbc
            // and use old one from cookie with old timestamp
            if ($existingFbclid !== $fbclid) {
                return $this->generateFbc($fbclid);
            }
        }

        // We have fbc cookie and shouldn't try to
        // regenerate it from fbclid (as it is empty)
        if (is_string($fbc)) {
            return $fbc;
        }

        // Have no fbc cookie, but have fbclid
        // to generate fbc from it
        if (is_string($fbclid)) {
            return $this->generateFbc($fbclid);
        }

        // We have no fbc cookie and no fbclid, so can't generate
        return null;
    }

    private function generateFbc(string $fbclid): string
    {
        $creationTime = ceil(microtime(true) * 1000);

        $fbc = sprintf(
            'fb.1.%s.%s',
            $creationTime,
            $fbclid
        );

        $this->generatedFbc = $fbc;

        return $fbc;
    }

    private function extractFbclid(string $fbc): ?string
    {
        if (false === preg_match('/fb\.1\.(\d+)\.(.+)/', $fbc, $m)) {
            return null;
        }

        if (!isset($m[2])) {
            return null;
        }

        /** @var string $fbclid */
        $fbclid = $m[2];

        return $fbclid;
    }
}
