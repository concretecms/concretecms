<?php

namespace Concrete\Core\Command\Task\Runner;

use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Entity\Command\Process;

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * Receives a command and asynchronously executes it on a process.
 */
class ProcessTaskRunner implements ProcessTaskRunnerInterface
{

    /**
     * @var object
     */
    protected $message;

    /**
     * @var string
     */
    protected $processStartedMessage;

    /**
     * @var TaskInterface
     */
    protected $task;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var Process
     */
    protected $process;

    public function __construct(
        TaskInterface $task,
        object $message,
        InputInterface $input,
        string $processStartedMessage
    ) {
        $this->task = $task;
        $this->message = $message;
        $this->processStartedMessage = $processStartedMessage;
        $this->input = $input;
    }

    /**
     * @return Process
     */
    public function getProcess(): Process
    {
        return $this->process;
    }

    /**
     * @param Process $process
     */
    public function setProcess(Process $process): void
    {
        $this->process = $process;
    }

    /**
     * @return TaskInterface
     */
    public function getTask(): TaskInterface
    {
        return $this->task;
    }

    /**
     * @return object
     */
    public function getMessage(): object
    {
        return $this->message;
    }

    /**
     * @return string
     */
    public function getProcessStartedMessage(): string
    {
        return $this->processStartedMessage;
    }

    /**
     * @return InputInterface
     */
    public function getInput(): ?InputInterface
    {
        return $this->input;
    }

    public function getTaskRunnerHandler(): string
    {
        return ProcessTaskRunnerHandler::class;
    }

}
