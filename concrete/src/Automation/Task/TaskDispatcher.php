<?php

namespace Concrete\Core\Automation\Task;

use Concrete\Core\Entity\Automation\Process;
use Concrete\Core\Foundation\Command\DispatcherFactory;
use Doctrine\ORM\EntityManager;

class TaskDispatcher
{

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var DispatcherFactory
     */
    protected $dispatcherFactory;

    public function __construct(EntityManager $entityManager, DispatcherFactory $dispatcherFactory)
    {
        $this->entityManager = $entityManager;
    }

    public function dispatch(Process $process): void
    {
        $controller = $process->getTask()->getController();

        $this->entityManager->persist($process);
        $this->entityManager->flush();
    }

}
