<?php

namespace Concrete\Core\Command\Process\Command;

use Concrete\Core\Command\Process\Logger\LoggerFactoryInterface;
use Concrete\Core\Entity\Command\Process;
use Doctrine\ORM\EntityManager;

class DeleteProcessCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var LoggerFactoryInterface
     */
    protected $loggerFactory;

    public function __construct(LoggerFactoryInterface $loggerFactory, EntityManager $entityManager)
    {
        $this->loggerFactory = $loggerFactory;
        $this->entityManager = $entityManager;
    }

    public function __invoke(DeleteProcessCommand $command)
    {
        $process = $this->entityManager->find(Process::class, $command->getProcessId());
        if ($process) {
            $logger = $this->loggerFactory->createFromProcess($process);
            if ($logger) {
                $logger->remove();
            }
            $batch = $process->getBatch();
            if ($batch) {
                $process->setBatch(null);
                $this->entityManager->remove($batch);
                $this->entityManager->flush();
            }
            $this->entityManager->remove($process);
            $this->entityManager->flush();
        }
    }

}