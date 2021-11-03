<?php

declare(strict_types=1);

namespace Setono\SyliusFacebookPlugin\Command;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Setono\SyliusFacebookPlugin\Client\ClientInterface;
use Setono\SyliusFacebookPlugin\Model\PixelEventInterface;
use Setono\SyliusFacebookPlugin\Repository\PixelEventRepositoryInterface;
use Setono\SyliusFacebookPlugin\Workflow\SendPixelEventWorkflow;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\WorkflowInterface;
use Webmozart\Assert\Assert;

final class SendEventsCommand extends Command
{
    protected static $defaultName = 'setono:sylius-facebook:send-pixel-events';

    private PixelEventRepositoryInterface $pixelEventRepository;

    private ClientInterface $client;

    private Registry $workflowRegistry;

    private EntityManagerInterface $entityManager;

    private int $delay;

    private ?WorkflowInterface $workflow = null;

    private ?ObjectManager $manager = null;

    public function __construct(
        PixelEventRepositoryInterface $pixelEventRepository,
        ClientInterface $client,
        Registry $workflowRegistry,
        EntityManagerInterface $entityManager,
        int $delay
    ) {
        parent::__construct();

        $this->pixelEventRepository = $pixelEventRepository;
        $this->client = $client;
        $this->workflowRegistry = $workflowRegistry;
        $this->entityManager = $entityManager;
        $this->delay = $delay;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        while ($this->pixelEventRepository->hasConsentedPending($this->delay)) {
            $bulkIdentifier = uniqid('bulk-', true);
            $this->pixelEventRepository->assignBulkIdentifierToPendingConsented($bulkIdentifier, $this->delay);

            $pixelEvents = $this->pixelEventRepository->findByBulkIdentifier($bulkIdentifier);
            foreach ($pixelEvents as $pixelEvent) {
                $workflow = $this->getWorkflow($pixelEvent);

                try {
                    if (!$workflow->can($pixelEvent, SendPixelEventWorkflow::TRANSITION_SEND)) {
                        continue;
                    }

                    $sentEvents = $this->client->sendPixelEvent($pixelEvent);
                    if ($sentEvents) {
                        $workflow->apply($pixelEvent, SendPixelEventWorkflow::TRANSITION_SEND);
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
