<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;

abstract class DelayAwareCommand extends Command
{
    protected int $defaultDelay;

    public function __construct(int $defaultDelay)
    {
        $this->defaultDelay = $defaultDelay;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'delay',
                'd',
                InputOption::VALUE_REQUIRED,
                'Handle events older than given amount of seconds',
                $this->defaultDelay
            )
        ;
    }
}
