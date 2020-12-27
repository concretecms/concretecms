<?php

namespace Concrete\Core\Command\Process\Command;

use Concrete\Core\Entity\Command\ScheduledTask;
use Doctrine\ORM\EntityManager;

class DeleteScheduledTaskCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(DeleteScheduledTaskCommand $command)
    {
        $scheduledTask = $this->entityManager->find(ScheduledTask::class, $command->getScheduledTaskId());
        if ($scheduledTask) {
            $this->entityManager->remove($scheduledTask);
            $this->entityManager->flush();
        }
    }

}