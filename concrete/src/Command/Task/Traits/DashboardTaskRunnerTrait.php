<?php

namespace Concrete\Core\Command\Task\Traits;

use Concrete\Core\Command\Task\Input\Input;
use Concrete\Core\Command\Task\Runner\Context\ContextFactory;
use Concrete\Core\Command\Task\TaskInterface;

trait DashboardTaskRunnerTrait
{

    public function executeTask(TaskInterface $task)
    {
        $controller = $task->getController();
        $runner = $controller->getTaskRunner($task, new Input());
        $handler = $this->app->make($runner->getTaskRunnerHandler());
        $handler->boot($runner);

        $contextFactory = $this->app->make(ContextFactory::class);
        $context = $contextFactory->createDashboardContext($runner);

        $handler->start($runner, $context);
        $handler->run($runner, $context);
    }


}
