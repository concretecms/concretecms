<?php

namespace Concrete\Core\Automation\Task\Runner;

use Concrete\Core\Automation\Task\Input\InputInterface;
use Concrete\Core\Automation\Task\Runner\Response\ProcessStartedResponse;
use Concrete\Core\Automation\Task\Runner\Response\ResponseInterface;
use Concrete\Core\Automation\Task\TaskInterface;
use Concrete\Core\Entity\Automation\Process;
use Concrete\Core\Foundation\Command\Command;

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * Receives a command and asynchronously executes it on a process.
 */
class ProcessTaskRunner extends Command implements TaskRunnerInterface
{

    /**
     * @var object
     */
    protected $message;

    /**
     * @var string
     */
    protected $responseMessage;

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
        string $responseMessage
    ) {
        $this->task = $task;
        $this->message = $message;
        $this->responseMessage = $responseMessage;
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
    public function getResponseMessage(): string
    {
        return $this->responseMessage;
    }

    public function getTaskRunnerResponse(): ResponseInterface
    {
        return new ProcessStartedResponse($this->process, $this->getResponseMessage());
    }

    /**
     * @return InputInterface
     */
    public function getInput(): ?InputInterface
    {
        return $this->input;
    }

}
