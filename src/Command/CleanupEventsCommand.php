<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Command;

use Setono\SyliusFacebookPlugin\Repository\PixelEventRepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class CleanupEventsCommand extends Command
{
    protected static $defaultName = 'setono:sylius-facebook:cleanup';

    private PixelEventRepositoryInterface $pixelEventRepository;

    public function __construct(PixelEventRepositoryInterface $pixelEventRepository)
    {
        parent::__construct();

        $this->pixelEventRepository = $pixelEventRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $removed = $this->pixelEventRepository->removeSent();
        $output->writeln(sprintf(
            '%d sent events removed from database.',
            $removed
        ));

        return 0;
    }
}
