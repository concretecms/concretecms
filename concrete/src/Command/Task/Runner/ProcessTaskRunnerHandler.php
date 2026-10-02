<?php
namespace Concrete\Core\Command\Task\Runner;

use Concrete\Core\Command\Process\Command\HandleProcessMessageCommand;
use Concrete\Core\Command\Process\ProcessFactory;
use Concrete\Core\Command\Task\Output\OutputInterface;
use Concrete\Core\Command\Task\Runner\Context\ContextInterface;
use Concrete\Core\Command\Task\Runner\Response\ProcessStartedResponse;
use Concrete\Core\Command\Task\Runner\Response\ResponseInterface;
use Concrete\Core\Command\Task\Stamp\OutputStamp;
use Concrete\Core\Command\Task\TaskService;
use Concrete\Core\Entity\Automation\Task;

defined('C5_EXECUTE') or die("Access Denied.");

class ProcessTaskRunnerHandler implements HandlerInterface
{

    /**
     * @var ProcessFactory
     */
    protected $processFactory;

    /**
     * @var TaskService
     */
    protected $taskService;

    public function __construct(TaskService $taskService, ProcessFactory $processFactory)
    {
        $this->taskService = $taskService;
        $this->processFactory = $processFactory;
    }

    public function boot(TaskRunnerInterface $runner)
    {
        if (!$runner instanceof ProcessTaskRunner && !$runner instanceof BatchProcessTaskRunner) {
            throw new \InvalidArgumentException(t('The task runner must be an instance of %s or %s.', ProcessTaskRunner::class, BatchProcessTaskRunner::class));
        }
        $task = $runner->getTask();
        if (!$task instanceof Task) {
            throw new \InvalidArgumentException(t('The task must be an instance of %s.', Task::class));
        }
        $this->taskService->start($task);
        $process = $this->processFactory->createTaskProcess($task, $runner->getInput());
        $runner->setProcess($process);
    }

    public function start(TaskRunnerInterface $runner, ContextInterface $context)
    {
        if (!$runner instanceof ProcessTaskRunner && !$runner instanceof BatchProcessTaskRunner) {
            throw new \InvalidArgumentException(t('The task runner must be an instance of %s or %s.', ProcessTaskRunner::class, BatchProcessTaskRunner::class));
        }
        $output = $context->getOutput();
        $output->write($runner->getProcessStartedMessage());
    }

    public function run(TaskRunnerInterface $runner, ContextInterface $context)
    {
        if (!$runner instanceof ProcessTaskRunner) {
            throw new \InvalidArgumentException(t('The task runner must be an instance of %s.', ProcessTaskRunner::class));
        }
        $process = $runner->getProcess();
        $wrappedMessage = new HandleProcessMessageCommand($process->getID(), $runner->getMessage());
        $context->dispatchCommand($wrappedMessage);
    }

    /**
     * Note: this returns a process started response because the completion of the task is actually just the beginning:
     * the process itself has been deferred via an async message, which will actually be done running at some later
     * point.
     */
    public function complete(TaskRunnerInterface $runner, ContextInterface $context): ResponseInterface
    {
        if (!$runner instanceof ProcessTaskRunner && !$runner instanceof BatchProcessTaskRunner) {
            throw new \InvalidArgumentException(t('The task runner must be an instance of %s or %s.', ProcessTaskRunner::class, BatchProcessTaskRunner::class));
        }

        return new ProcessStartedResponse($runner->getProcess(), $runner->getProcessStartedMessage());
    }


}
