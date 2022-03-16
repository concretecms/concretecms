<?php

namespace Concrete\Core\Command\Task\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Command\Process\ProcessUpdater;
use Concrete\Core\Command\Task\Runner\Context\ContextFactory;
use Concrete\Core\Messenger\MessageBusManager;

class ExecuteConsoleTaskCommandHandler
{

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var ContextFactory
     */
    protected $contextFactory;

    /**
     * @var MessageBusManager
     */
    protected $messageBusManager;

    /**
     * @var ProcessUpdater
     */
    protected $processUpdater;


    public function __construct(
        Application $app,
        ContextFactory $contextFactory,
        MessageBusManager $messageBusManager,
        ProcessUpdater $processUpdater
    ) {
        $this->app = $app;
        $this->contextFactory = $contextFactory;
        $this->messageBusManager = $messageBusManager;
        $this->processUpdater = $processUpdater;
    }


    public function __invoke(ExecuteConsoleTaskCommand $command)
    {
        $task = $command->getTask();
        $runner = $task->getController()->getTaskRunner($task, $command->getInput());
        $handler = $this->app->make($runner->getTaskRunnerHandler());

        $handler->boot($runner);

        $context = $this->contextFactory->createConsoleContext($runner, $command->getOutput()); // Must come after boot.

        $handler->start($runner, $context);
        $handler->run($runner, $context);
        $handler->complete($runner, $context);
    }

}