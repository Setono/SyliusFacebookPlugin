<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Twig;

use Setono\SyliusFacebookPlugin\Model\PixelInterface;
use Setono\SyliusFacebookPlugin\Repository\PixelEventRepositoryInterface;
use Setono\SyliusFacebookPlugin\Workflow\SendPixelEventWorkflow;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class EventsPushingStatisticsExtension extends AbstractExtension
{
    private PixelEventRepositoryInterface $pixelEventRepository;

    public function __construct(PixelEventRepositoryInterface $pixelEventRepository)
    {
        $this->pixelEventRepository = $pixelEventRepository;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('setono_facebook_events_pushing_statistics', [$this, 'getEventsPushingStatistics']),
            new TwigFunction('setono_facebook_events_count_by_state', [$this, 'getEventsCountByState']),
        ];
    }

    /**
     * @return array<string, int>
     */
    public function getEventsPushingStatistics(PixelInterface $pixel): array
    {
        $result = [];
        foreach (SendPixelEventWorkflow::getStates() as $state) {
            $count = $this->pixelEventRepository->getCountByPixelAndState($pixel, $state);
            if (0 === $count) {
                continue;
            }

            $result[$state] = $count;
        }

        return $result;
    }

    public function getEventsCountByState(PixelInterface $pixel, string $state): int
    {
        return $this->pixelEventRepository->getCountByPixelAndState($pixel, $state);
    }
}
