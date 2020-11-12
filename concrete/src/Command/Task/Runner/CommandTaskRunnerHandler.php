<?php
namespace Concrete\Core\Command\Task\Runner;

use Concrete\Core\Command\Task\TaskService;
use Symfony\Component\Messenger\MessageBusInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class CommandTaskRunnerHandler
{

    /**
     * @var MessageBusInterface
     */
    protected $messageBus;

    /**
     * @var TaskService
     */
    protected $taskService;

    public function __construct(TaskService $taskService, MessageBusInterface $messageBus)
    {
        $this->taskService = $taskService;
        $this->messageBus = $messageBus;
    }

    public function __invoke(CommandTaskRunner $command)
    {
        $this->taskService->start($command->getTask());

        $this->messageBus->dispatch($command->getCommand());

        $this->taskService->complete($command->getTask());

    }


}
