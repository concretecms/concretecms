<?php

namespace Concrete\Core\Automation\Task\Runner;

use Concrete\Core\Automation\Task\Runner\Response\ResponseInterface;
use Concrete\Core\Foundation\Command\DispatcherFactory;
use Concrete\Core\Foundation\Command\SynchronousBus;

class TaskRunner
{

    /**
     * @var DispatcherFactory
     */
    protected $dispatcherFactory;

    public function __construct(DispatcherFactory $dispatcherFactory)
    {
        $this->dispatcherFactory = $dispatcherFactory;
    }

    public function run(TaskRunnerInterface $runner): ResponseInterface
    {
        $dispatcher = $this->dispatcherFactory->getDispatcher();
        $dispatcher->dispatch($runner, SynchronousBus::getHandle());
        return $runner->getTaskRunnerResponse();
    }

}
