<?php

namespace Concrete\Core\Command\Process;

use Concrete\Core\Command\Batch\Batch as PendingBatch;
use Concrete\Core\Command\Batch\BatchUpdater;
use Concrete\Core\Command\Batch\Stamp\BatchStamp;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Entity\Command\Batch;
use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Entity\Command\TaskProcess;
use Concrete\Core\Localization\Service\Date;
use Concrete\Core\User\User;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Messenger\MessageBusInterface;

class ProcessFactory
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
     * @var BatchUpdater
     */
    protected $batchUpdater;

    public function __construct(
        EntityManager $entityManager,
        Date $dateService,
        MessageBusInterface $messageBus,
        BatchUpdater $batchUpdater
    ) {
        $this->entityManager = $entityManager;
        $this->dateService = $dateService;
        $this->messageBus = $messageBus;
        $this->batchUpdater = $batchUpdater;
    }

    public function createTaskProcess(TaskInterface $task, InputInterface $input = null)
    {
        $process = new TaskProcess();
        $process->setTask($task);
        $process->setName($task->getController()->getName());
        $process->setInput($input);
        return $this->fillProcess($process);
    }

    public function createProcess(string $name): Process
    {
        $process = new Process();
        $process->setName($name);
        return $this->fillProcess($process);
    }

    protected function fillProcess(Process $process): Process
    {
        $process->setDateStarted($this->dateService->toDateTime()->getTimestamp());
        $user = new User();
        if ($user) {
            $userInfo = $user->getUserInfoObject();
            if ($userInfo) {
                $process->setUser($userInfo->getEntityObject());
            }
        }
        $this->entityManager->persist($process);
        $this->entityManager->flush();
        return $process;
    }

    public function createBatchEntity(PendingBatch $batch): Batch
    {
        $batchEntity = new Batch();
        $this->entityManager->persist($batchEntity);
        $this->entityManager->flush();

        return $batchEntity;
    }

    public function setBatchTotal(Batch $batchEntity, Process $process, $totalJobs)
    {
        $this->batchUpdater->updateJobs($batchEntity->getID(), BatchUpdater::COLUMN_TOTAL, $totalJobs);
        $this->batchUpdater->updateJobs($batchEntity->getID(), BatchUpdater::COLUMN_PENDING, $totalJobs);
        $this->entityManager->refresh($batchEntity);
        $this->entityManager->persist($process);
        $this->entityManager->flush();
    }

    /**
     * @param PendingBatch $batch
     */
    public function createWithBatch(PendingBatch $batch): Process
    {
        $batchEntity = $this->createBatchEntity($batch);
        $process = $this->createProcess($batch->getName());
        $process->setBatch($batchEntity);

        $total = 0;
        foreach ($batch->getWrappedMessages($batchEntity) as $message) {
            $this->messageBus->dispatch($message, [new BatchStamp($batchEntity->getId())]);
            $total++;
        }

        $this->setBatchTotal($batchEntity, $process, $total);

        return $process;
    }


}
