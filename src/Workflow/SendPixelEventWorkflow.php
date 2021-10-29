<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Workflow;

use Setono\SyliusFacebookPlugin\Model\PixelEventInterface;

final class SendPixelEventWorkflow
{
    public const NAME = 'setono_facebook_send_pixel_event';

    public const TRANSITION_SEND = 'send';

    public const TRANSITION_FAIL = 'fail';

    private function __construct()
    {
    }

    /**
     * @return array<array-key, string>
     */
    public static function getStates(): array
    {
        return [
            PixelEventInterface::STATE_PENDING,
            PixelEventInterface::STATE_SENT,
            PixelEventInterface::STATE_FAILED,
        ];
    }

    public static function getConfig(): array
    {
        return [
            self::NAME => [
                'type' => 'state_machine',
                'marking_store' => [
                    'type' => 'method',
                    'property' => 'state',
                ],
                'supports' => PixelEventInterface::class,
                'initial_marking' => PixelEventInterface::STATE_PENDING,
                'places' => self::getStates(),
                'transitions' => [
                    self::TRANSITION_SEND => [
                        'from' => PixelEventInterface::STATE_PENDING,
                        'to' => PixelEventInterface::STATE_SENT,
                    ],
                    self::TRANSITION_FAIL => [
                        'from' => PixelEventInterface::STATE_PENDING,
                        'to' => PixelEventInterface::STATE_FAILED,
                    ],
                ],
            ],
        ];
    }
}
