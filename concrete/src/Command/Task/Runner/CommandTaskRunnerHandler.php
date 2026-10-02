<?php
namespace Concrete\Core\Command\Task\Runner;

use Concrete\Core\Command\Task\Output\OutputInterface;
use Concrete\Core\Command\Task\Runner\Context\ContextInterface;
use Concrete\Core\Command\Task\Runner\Response\ResponseInterface;
use Concrete\Core\Command\Task\Runner\Response\TaskCompletedResponse;
use Concrete\Core\Command\Task\Stamp\OutputStamp;
use Concrete\Core\Command\Task\TaskService;
use Concrete\Core\Entity\Automation\Task;
use Symfony\Component\Messenger\MessageBusInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class CommandTaskRunnerHandler implements HandlerInterface
{

    /**
     * @var TaskService
     */
    protected $taskService;

    /**
     * @var MessageBusInterface
     */
    protected $messageBus;

    public function __construct(TaskService $taskService, MessageBusInterface $messageBus)
    {
        $this->taskService = $taskService;
        $this->messageBus = $messageBus;
    }

    public function boot(TaskRunnerInterface $runner)
    {
        if (!$runner instanceof CommandTaskRunner) {
            throw new \InvalidArgumentException(t('The task runner must be an instance of %s.', CommandTaskRunner::class));
        }
        $task = $runner->getTask();
        if (!$task instanceof Task) {
            throw new \InvalidArgumentException(t('The task must be an instance of %s.', Task::class));
        }
        $this->taskService->start($task);
    }

    public function start(TaskRunnerInterface $runner, ContextInterface $context)
    {
        // Nothing.
    }

    public function run(TaskRunnerInterface $runner, ContextInterface $context)
    {
        if (!$runner instanceof CommandTaskRunner) {
            throw new \InvalidArgumentException(t('The task runner must be an instance of %s.', CommandTaskRunner::class));
        }
        $message = $runner->getCommand();
        $context->dispatchCommand($message);
    }

    public function complete(TaskRunnerInterface $runner, ContextInterface $context): ResponseInterface
    {
        if (!$runner instanceof CommandTaskRunner) {
            throw new \InvalidArgumentException(t('The task runner must be an instance of %s.', CommandTaskRunner::class));
        }
        $task = $runner->getTask();
        if (!$task instanceof Task) {
            throw new \InvalidArgumentException(t('The task must be an instance of %s.', Task::class));
        }
        $output = $context->getOutput();
        $output->write($runner->getCompletionMessage());
        $this->taskService->complete($task);
        return new TaskCompletedResponse($runner->getCompletionMessage());
    }

}
