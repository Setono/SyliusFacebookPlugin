<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Command;

use Doctrine\ORM\EntityManagerInterface;
use Setono\SyliusFacebookPlugin\Client\ClientInterface;
use Setono\SyliusFacebookPlugin\Model\PixelEventInterface;
use Setono\SyliusFacebookPlugin\Repository\PixelEventRepositoryInterface;
use Setono\SyliusFacebookPlugin\Workflow\SendPixelEventWorkflow;
use Symfony\Component\Console\Command\LockableTrait;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\WorkflowInterface;
use Webmozart\Assert\Assert;

final class SendEventsCommand extends DelayAwareCommand
{
    use LockableTrait;

    protected static $defaultName = 'setono:sylius-facebook:send-pixel-events';

    private PixelEventRepositoryInterface $pixelEventRepository;

    private ClientInterface $client;

    private Registry $workflowRegistry;

    private EntityManagerInterface $entityManager;

    private ?WorkflowInterface $workflow = null;

    public function __construct(
        PixelEventRepositoryInterface $pixelEventRepository,
        ClientInterface $client,
        Registry $workflowRegistry,
        EntityManagerInterface $entityManager,
        int $defaultDelay
    ) {
        $this->pixelEventRepository = $pixelEventRepository;
        $this->client = $client;
        $this->workflowRegistry = $workflowRegistry;
        $this->entityManager = $entityManager;

        parent::__construct($defaultDelay);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (!$this->lock()) {
            $output->writeln('The command is already running in another process.');

            return 0;
        }

        $delay = $input->getOption('delay');
        Assert::integerish($delay);

        while ($this->pixelEventRepository->hasConsentedPending((int) $delay)) {
            $bulkIdentifier = uniqid('bulk-', true);
            $this->pixelEventRepository->assignBulkIdentifierToPendingConsented($bulkIdentifier, (int) $delay);

            $pixelEvents = $this->pixelEventRepository->findByBulkIdentifier($bulkIdentifier);
            $output->writeln(sprintf(
                'Found %s events to send.',
                count($pixelEvents)
            ), OutputInterface::VERBOSITY_VERBOSE);

            foreach ($pixelEvents as $pixelEvent) {
                $workflow = $this->getWorkflow($pixelEvent);

                try {
                    if (!$workflow->can($pixelEvent, SendPixelEventWorkflow::TRANSITION_SEND)) {
                        $output->writeln(sprintf(
                            'Unable to send %s event #%s with state %s.',
                            (string) $pixelEvent->getEventName(),
                            $pixelEvent->getId(),
                            $pixelEvent->getState()
                        ));

                        continue;
                    }

                    $sentEvents = $this->client->sendPixelEvent($pixelEvent);
                    if ($sentEvents) {
                        $workflow->apply($pixelEvent, SendPixelEventWorkflow::TRANSITION_SEND);
                        $output->writeln(sprintf(
                            '%s event #%s was sent.',
                            (string) $pixelEvent->getEventName(),
                            $pixelEvent->getId()
                        ), OutputInterface::VERBOSITY_VERY_VERBOSE);
                    } else {
                        $workflow->apply($pixelEvent, SendPixelEventWorkflow::TRANSITION_FAIL);
                    }
                } catch (\Throwable $e) {
                    $workflow->apply($pixelEvent, SendPixelEventWorkflow::TRANSITION_FAIL);
                }

                $this->entityManager->flush();
            }

            $this->entityManager->clear();
        }

        return 0;
    }

    private function getWorkflow(PixelEventInterface $pixelEvent): WorkflowInterface
    {
        if (null === $this->workflow) {
            Assert::true($this->workflowRegistry->has($pixelEvent, SendPixelEventWorkflow::NAME));

            $this->workflow = $this->workflowRegistry->get($pixelEvent, SendPixelEventWorkflow::NAME);
        }

        return $this->workflow;
    }
}
