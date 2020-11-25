<?php

namespace Concrete\Core\Command\Task\Runner;

use Concrete\Core\Command\Batch\Batch;
use Concrete\Core\Command\Task\Input\InputInterface;
use Concrete\Core\Command\Task\Runner\Response\ProcessStartedResponse;
use Concrete\Core\Command\Task\Runner\Response\ResponseInterface;
use Concrete\Core\Command\Task\TaskInterface;
use Concrete\Core\Entity\Command\Process;
use Concrete\Core\Foundation\Command\Command;

defined('C5_EXECUTE') or die("Access Denied.");

class BatchProcessTaskRunner extends Command implements TaskRunnerInterface
{


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

    /**
     * @var Batch
     */
    protected $batch;

    public function __construct(
        TaskInterface $task,
        Batch $batch,
        InputInterface $input,
        string $responseMessage
    ) {
        $this->task = $task;
        $this->batch = $batch;
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
     * @return Batch
     */
    public function getBatch(): Batch
    {
        return $this->batch;
    }

    /**
     * @return InputInterface
     */
    public function getInput(): ?InputInterface
    {
        return $this->input;
    }


}
