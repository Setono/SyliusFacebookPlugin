<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\DataMapper;

use Setono\SyliusFacebookPlugin\ServerSide\ServerSideEventInterface;
use Symfony\Component\HttpFoundation\Request;

/* not final */ class RequestDataMapper implements DataMapperInterface
{
    /**
     * @psalm-assert-if-true Request $context['request']
     */
    public function supports($source, ServerSideEventInterface $target, array $context = []): bool
    {
        return isset($context['request'])
            && $context['request'] instanceof Request;
    }

    /**
     * @param array{request: Request} $context
     */
    public function map($source, ServerSideEventInterface $target, array $context = []): void
    {
        /** @var Request $request */
        $request = $context['request'];

        $target
            ->setEventSourceUrl($request->getUri())
        ;

        $userData = $target->getUserData();
        $userData
            ->setClientIpAddress($request->getClientIp())
            ->setClientUserAgent($request->headers->get('User-Agent'))
        ;
    }
}
