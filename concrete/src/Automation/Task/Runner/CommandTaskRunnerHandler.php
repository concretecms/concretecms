<?php
namespace Concrete\Core\Automation\Task\Runner;

use Concrete\Core\Automation\Task\Response\ResponseInterface;
use Concrete\Core\Automation\Task\Response\TaskCompletedResponse;
use Concrete\Core\Automation\Task\TaskService;
use Concrete\Core\Foundation\Command\Command;
use Concrete\Core\Foundation\Command\CommandInterface;
use Concrete\Core\Foundation\Command\DispatcherFactory;
use Concrete\Core\Foundation\Command\SynchronousBus;

defined('C5_EXECUTE') or die("Access Denied.");

class CommandTaskRunnerHandler
{

    /**
     * @var DispatcherFactory
     */
    protected $dispatcherFactory;

    /**
     * @var TaskService
     */
    protected $taskService;

    public function __construct(TaskService $taskService, DispatcherFactory $dispatcherFactory)
    {
        $this->taskService = $taskService;
        $this->dispatcherFactory = $dispatcherFactory;
    }

    public function handle(CommandTaskRunner $command)
    {
        $this->taskService->start($command->getTask());

        $dispatcher = $this->dispatcherFactory->getDispatcher();
        $dispatcher->dispatch($command->getCommand());

        $this->taskService->complete($command->getTask());

    }


}
