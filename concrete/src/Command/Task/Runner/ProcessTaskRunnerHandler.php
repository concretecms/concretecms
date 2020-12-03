<?php
namespace Concrete\Core\Command\Task\Runner;

use Concrete\Core\Command\Process\Command\HandleProcessMessageCommand;
use Concrete\Core\Command\Process\ProcessFactory;
use Concrete\Core\Command\Task\Output\OutputInterface;
use Concrete\Core\Command\Task\Runner\Response\ProcessStartedResponse;
use Concrete\Core\Command\Task\Runner\Response\ResponseInterface;
use Concrete\Core\Command\Task\Runner\Response\TaskCompletedResponse;
use Concrete\Core\Command\Task\Stamp\OutputStamp;
use Concrete\Core\Command\Task\TaskService;
use Concrete\Core\Messenger\MessageBusAwareInterface;
use Concrete\Core\Messenger\MessageBusAwareTrait;
use Symfony\Component\Messenger\MessageBusInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class ProcessTaskRunnerHandler implements HandlerInterface, MessageBusAwareInterface
{

    use MessageBusAwareTrait;

    /**
     * @var MessageBusInterface
     */
    protected $messageBus;

    /**
     * @var ProcessFactory
     */
    protected $processFactory;

    /**
     * @var TaskService
     */
    protected $taskService;

    public function __construct(TaskService $taskService, ProcessFactory $processFactory, MessageBusInterface $messageBus)
    {
        $this->taskService = $taskService;
        $this->processFactory = $processFactory;
        $this->messageBus = $messageBus;
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

    public function start(TaskRunnerInterface $runner, OutputInterface $output)
    {
        $output->write($runner->getProcessStartedMessage());
    }

    public function run(TaskRunnerInterface $runner, OutputInterface $output)
    {
        $process = $runner->getProcess();
        $wrappedMessage = new HandleProcessMessageCommand($process->getID(), $runner->getMessage());
        $this->messageBus->dispatch($wrappedMessage, [new OutputStamp($output)]);
    }

    /**
     * Note: this returns a process started response because the completion of the task is actually just the beginning:
     * the process itself has been deferred via an async message, which will actually be done running at some later
     * point.
     *
     * @param ProcessTaskRunner $runner
     */
    public function complete(TaskRunnerInterface $runner, OutputInterface $output): ResponseInterface
    {
        return new ProcessStartedResponse($runner->getProcess(), $runner->getProcessStartedMessage());
    }


}
