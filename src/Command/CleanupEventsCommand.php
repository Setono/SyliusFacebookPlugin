<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Command;

use Setono\SyliusFacebookPlugin\Repository\PixelEventRepositoryInterface;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Webmozart\Assert\Assert;

final class CleanupEventsCommand extends DelayAwareCommand
{
    protected static $defaultName = 'setono:sylius-facebook:cleanup';

    private PixelEventRepositoryInterface $pixelEventRepository;

    public function __construct(
        PixelEventRepositoryInterface $pixelEventRepository,
        int $defaultDelay
    ) {
        $this->pixelEventRepository = $pixelEventRepository;

        parent::__construct($defaultDelay);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $delay = $input->getOption('delay');
        Assert::integerish($delay);

        $removed = $this->pixelEventRepository->removeSent((int) $delay);
        $output->writeln(sprintf(
            '%d sent events removed from database.',
            $removed
        ));

        return 0;
    }
}
