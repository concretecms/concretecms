<?php
namespace Concrete\Core\Command\Task\Runner;

use Concrete\Core\Command\Task\TaskInterface;

defined('C5_EXECUTE') or die("Access Denied.");

/**
 * Receives a task and a command, and synchronously executes the command. On complete, returns the response.
 */
class CommandTaskRunner implements TaskRunnerInterface
{

    /**
     * @var object
     */
    protected $command;

    /**
     * @var string
     */
    protected $completionMessage;

    /**
     * @var TaskInterface
     */
    protected $task;

    public function __construct(TaskInterface $task, object $command, string $completionMessage)
    {
        $this->task = $task;
        $this->command = $command;
        $this->completionMessage = $completionMessage;
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
    public function getCompletionMessage(): string
    {
        return $this->completionMessage;
    }

    public function getTaskRunnerHandler(): string
    {
        return CommandTaskRunnerHandler::class;
    }

}
