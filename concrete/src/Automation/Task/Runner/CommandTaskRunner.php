<?php
namespace Concrete\Core\Automation\Task\Runner;

use Concrete\Core\Automation\Task\Runner\Response\ResponseInterface;
use Concrete\Core\Automation\Task\Runner\Response\TaskCompletedResponse;
use Concrete\Core\Automation\Task\TaskInterface;
use Concrete\Core\Foundation\Command\Command;

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * Receives a task and a command, and synchronously executes the command. On complete, returns the response.
 */
class CommandTaskRunner extends Command implements TaskRunnerInterface
{

    /**
     * @var object
     */
    protected $command;

    /**
     * @var string
     */
    protected $responseMessage;

    /**
     * @var TaskInterface
     */
    protected $task;

    public function __construct(TaskInterface $task, object $command, string $responseMessage)
    {
        $this->task = $task;
        $this->command = $command;
        $this->responseMessage = $responseMessage;
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
    public function getCommand(): object
    {
        return $this->command;
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
        return new TaskCompletedResponse($this->getResponseMessage());
    }


}
