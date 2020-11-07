<?php

namespace Concrete\Core\Automation\Task\Runner;

use Concrete\Core\Automation\Task\Runner\Response\ResponseInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class TaskRunner
{

    /**
     * @var MessageBusInterface
     */
    protected $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function run(TaskRunnerInterface $runner): ResponseInterface
    {
        $this->messageBus->dispatch($runner);
        return $runner->getTaskRunnerResponse();
    }

}
