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

    /**
     * @param ProcessTaskRunner $runner
     */
    public function boot(TaskRunnerInterface $runner)
    {
        $this->taskService->start($runner->getTask());
        $process = $this->processFactory->createTaskProcess($runner->getTask(), $runner->getInput());
        $runner->setProcess($process);
    }

    public function start(TaskRunnerInterface $runner, ContextInterface $context)
    {
        $output = $context->getOutput();
        $output->write($runner->getProcessStartedMessage());
    }

    public function run(TaskRunnerInterface $runner, ContextInterface $context)
    {
        $output = $context->getOutput();
        $messageBus = $context->getMessageBus();
        $process = $runner->getProcess();
        $wrappedMessage = new HandleProcessMessageCommand($process->getID(), $runner->getMessage());
        $context->dispatchCommand($wrappedMessage);
    }

    /**
     * Note: this returns a process started response because the completion of the task is actually just the beginning:
     * the process itself has been deferred via an async message, which will actually be done running at some later
     * point.
     *
     * @param ProcessTaskRunner $runner
     */
    public function complete(TaskRunnerInterface $runner, ContextInterface $context): ResponseInterface
    {
        return new ProcessStartedResponse($runner->getProcess(), $runner->getProcessStartedMessage());
    }


}
