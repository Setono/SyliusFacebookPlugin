<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Controller\Action\Pixel;

use Setono\SyliusFacebookPlugin\Model\PixelInterface;
use Setono\SyliusFacebookPlugin\Repository\PixelEventRepositoryInterface;
use Setono\SyliusFacebookPlugin\Repository\PixelRepositoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Webmozart\Assert\Assert;

final class ResetFailedEventsAction
{
    private PixelRepositoryInterface $pixelRepository;

    private CsrfTokenManagerInterface $csrfTokenManager;

    private PixelEventRepositoryInterface $pixelEventRepository;

    private UrlGeneratorInterface $urlGeneratorInterface;

    public function __construct(
        PixelRepositoryInterface $pixelRepository,
        CsrfTokenManagerInterface $csrfTokenManager,
        PixelEventRepositoryInterface $pixelEventRepository,
        UrlGeneratorInterface $urlGeneratorInterface
    ) {
        $this->pixelRepository = $pixelRepository;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->pixelEventRepository = $pixelEventRepository;
        $this->urlGeneratorInterface = $urlGeneratorInterface;
    }

    public function __invoke(Request $request, int $id): Response
    {
        /** @var FlashBagInterface $flashBag */
        $flashBag = $request->getSession()->getBag('flashes');

        /** @var PixelInterface|null $pixel */
        $pixel = $this->pixelRepository->find($id);
        Assert::notNull($pixel);

        $csrfToken = $request->request->get('_csrf_token');
        Assert::string($csrfToken);
        if (!$this->isCsrfTokenValid((string) $pixel->getId(), $csrfToken)) {
            throw new HttpException(Response::HTTP_FORBIDDEN, 'Invalid csrf token.');
        }

        $this->pixelEventRepository->resetFailedByPixel($pixel);

        $flashBag->add('success', 'setono_sylius_facebook.pixel.failed_events_reset');

        return new RedirectResponse(
            $this->getRedirectUrl($request, 'setono_sylius_facebook_admin_pixel_index')
        );
    }

    private function getRedirectUrl(Request $request, string $defaultRoute): string
    {
        $syliusParameters = [];

        if ($request->attributes->has('_sylius')) {
            /** @var array|mixed $syliusParameters */
            $syliusParameters = $request->attributes->get('_sylius');
            Assert::isArray($syliusParameters);
        }

        /** @var string|mixed $route */
        $route = $syliusParameters['redirect']['route'] ?? $defaultRoute;
        Assert::string($route);

        /** @var array|mixed $parameters */
        $parameters = $syliusParameters['redirect']['parameters'] ?? [];
        Assert::isArray($parameters);

        return $this->urlGeneratorInterface->generate($route, $parameters);
    }

    private function isCsrfTokenValid(string $id, ?string $token): bool
    {
        return $this->csrfTokenManager->isTokenValid(new CsrfToken($id, $token));
    }
}
