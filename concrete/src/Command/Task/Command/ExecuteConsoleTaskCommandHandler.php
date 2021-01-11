<?php

namespace Concrete\Core\Command\Task\Command;

use Concrete\Core\Application\Application;
use Concrete\Core\Command\Process\Command\ProcessMessageInterface;
use Concrete\Core\Command\Process\ProcessUpdater;
use Concrete\Core\Command\Task\Input\InputFactory;
use Concrete\Core\Command\Task\Output\OutputFactory;
use Concrete\Core\Command\Task\Runner\ProcessTaskRunnerInterface;
use Concrete\Core\Messenger\MessageBusAwareInterface;
use Concrete\Core\Messenger\MessageBusManager;

class ExecuteConsoleTaskCommandHandler
{

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var OutputFactory
     */
    protected $outputFactory;

    /**
     * @var MessageBusManager
     */
    protected $messageBusManager;

    /**
     * @var ProcessUpdater
     */
    protected $processUpdater;

    /**
     * ExecuteConsoleTaskCommandHandler constructor.
     * @param Application $app
     * @param InputFactory $inputFactory
     * @param OutputFactory $outputFactory
     * @param MessageBusManager $messageBusManager
     * @param ProcessUpdater $processUpdater
     */
    public function __construct(
        Application $app,
        OutputFactory $outputFactory,
        MessageBusManager $messageBusManager,
        ProcessUpdater $processUpdater
    ) {
        $this->app = $app;
        $this->outputFactory = $outputFactory;
        $this->messageBusManager = $messageBusManager;
        $this->processUpdater = $processUpdater;
    }


    public function __invoke(ExecuteConsoleTaskCommand $command)
    {
        $task = $command->getTask();
        $runner = $task->getController()->getTaskRunner($task, $command->getInput());
        $handler = $this->app->make($runner->getTaskRunnerHandler());

        if ($handler instanceof MessageBusAwareInterface) {
            $handler->setMessageBus($this->messageBusManager->getBus(MessageBusManager::BUS_DEFAULT_SYNCHRONOUS));
        }

        $handler->boot($runner);

        $taskOutput = $this->outputFactory->createConsoleOutput($command->getOutput(), $runner); // Must come after boot.

        $handler->start($runner, $taskOutput);
        $handler->run($runner, $taskOutput);
        $handler->complete($runner, $taskOutput);

        if ($runner instanceof ProcessTaskRunnerInterface) {
            $this->processUpdater->closeProcess($runner->getProcess(), ProcessMessageInterface::EXIT_CODE_SUCCESS);
        }
    }

}