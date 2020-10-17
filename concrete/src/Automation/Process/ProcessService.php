<?php

namespace Concrete\Core\Automation\Process;

use Concrete\Core\Automation\AbstractService;
use Concrete\Core\Automation\Task\Input\InputInterface;
use Concrete\Core\Automation\Task\TaskService;
use Concrete\Core\Entity\Automation\Process;
use Concrete\Core\Entity\Automation\Task;
use Concrete\Core\Foundation\Queue\QueueService;
use Doctrine\ORM\EntityManager;

/**
 * Methods useful for working with Process objects.
 */
class ProcessService
{

    /**
     * @var TaskService
     */
    protected $taskService;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    public function __construct(TaskService $taskService, EntityManager $entityManager)
    {
        $this->taskService = $taskService;
        $this->entityManager = $entityManager;
    }

    public function createProcess(Task $task, InputInterface $input, string $transport): Process
    {
        $this->taskService->start($task);

        $process = new Process();
        $process->setDateStarted($task->getDateLastStarted());
        $process->setInput($input);;
        $process->setTask($task);
        $process->setUser($task->getLastRunBy());
        $process->setTransport($transport);

        $this->entityManager->persist($process);
        $this->entityManager->flush();

        return $process;
    }



}
