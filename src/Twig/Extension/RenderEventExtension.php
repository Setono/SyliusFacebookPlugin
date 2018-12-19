<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookTrackingPlugin\Twig\Extension;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Templating\EngineInterface;
use Twig\Extension\AbstractExtension;

final class RenderEventExtension extends AbstractExtension
{
    /** @var SessionInterface */
    private $session;

    /** @var EngineInterface */
    private $templatingEngine;

    public function __construct(SessionInterface $session, EngineInterface $templatingEngine)
    {
        $this->session = $session;
        $this->templatingEngine = $templatingEngine;
    }

    public function getFunctions(): array
    {
        return [
            new \Twig_Function('render_facebook_events', [$this, 'renderFacebookEvents'], ['is_safe' => ['html']]),
        ];
    }

    public function renderFacebookEvents(): string
    {
        $facebookEvents = $this->session->get('facebook_events');

        $this->session->remove('facebook_events');

        return $this->templatingEngine->render('SetonoSyliusFacebookTrackingPlugin::facebook_events.html.twig', ['events' => $facebookEvents]);
    }
}
