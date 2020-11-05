<?php

namespace Concrete\Core\Messenger\Batch;

use Concrete\Core\Entity\Messenger\BatchProcess;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Messenger\MessageBusInterface;
use Concrete\Core\Messenger\Batch\Command\HandleBatchMessageCommand;

class BatchProcessor
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var Date
     */
    protected $dateService;

    /**
     * @var MessageBusInterface
     */
    protected $messageBus;

    /**
     * @var BatchProcessUpdater
     */
    protected $batchProcessUpdater;

    public function __construct(
        EntityManager $entityManager,
        Date $dateService,
        MessageBusInterface $messageBus,
        BatchProcessUpdater $batchProcessUpdater
    ) {
        $this->entityManager = $entityManager;
        $this->dateService = $dateService;
        $this->messageBus = $messageBus;
        $this->batchProcessUpdater = $batchProcessUpdater;
    }

    /**
     * @param iterable|callable $messages
     * @param string $name
     * @return Batch
     */
    public function createBatch($messages, string $name)
    {
        if (is_callable($messages)) {
            $messages = $messages();
        }
        return (new Batch())->setName($name)->setMessages($messages);
    }

    /**
     * @param Batch $batch
     */
    public function dispatch(Batch $batch): BatchProcess
    {
        // First, create the underlying batch object
        $process = new BatchProcess();
        $process->setDateStarted($this->dateService->toDateTime()->getTimestamp());
        $process->setName($batch->getName());

        $user = new User();
        if ($user) {
            $userInfo = $user->getUserInfoObject();
            if ($userInfo) {
                $process->setUser($userInfo->getEntityObject());
            }
        }

        $this->entityManager->persist($process);
        $this->entityManager->flush();

        $totalJobs = 0;
        foreach ($batch->getMessages() as $message) {
            $command = new HandleBatchMessageCommand($process->getID(), $message);
            $this->messageBus->dispatch($command);
            $totalJobs++;
        }

        $this->batchProcessUpdater->updateJobs($process->getID(), BatchProcessUpdater::COLUMN_TOTAL, $totalJobs);
        $this->batchProcessUpdater->updateJobs($process->getID(), BatchProcessUpdater::COLUMN_PENDING, $totalJobs);
        $this->entityManager->refresh($process);
        
        return $process;
    }


}
