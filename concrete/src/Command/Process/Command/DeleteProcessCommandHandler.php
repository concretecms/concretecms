<?php

namespace Concrete\Core\Command\Process\Command;

use Concrete\Core\Entity\Command\Process;
use Doctrine\ORM\EntityManager;

class DeleteProcessCommandHandler
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(DeleteProcessCommand $command)
    {
        $process = $this->entityManager->find(Process::class, $command->getProcessId());
        if ($process) {
            $this->entityManager->remove($process);
            $this->entityManager->flush();
        }
    }

}