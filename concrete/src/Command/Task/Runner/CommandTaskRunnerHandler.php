<?php
namespace Concrete\Core\Command\Task\Runner;

use Concrete\Core\Command\Task\Output\OutputInterface;
use Concrete\Core\Command\Task\Runner\Response\ResponseInterface;
use Concrete\Core\Command\Task\Runner\Response\TaskCompletedResponse;
use Concrete\Core\Command\Task\Stamp\OutputStamp;
use Concrete\Core\Command\Task\TaskService;
use Concrete\Core\Task\Output\OutputAwareInterface;
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
        $this->taskService->start($runner->getTask());
    }

    public function start(TaskRunnerInterface $runner, OutputInterface $output)
    {
        // Nothing.
    }

    public function run(TaskRunnerInterface $runner, OutputInterface $output)
    {
        $message = $runner->getCommand();
        $this->messageBus->dispatch($message, [new OutputStamp($output)]);
    }

    public function complete(TaskRunnerInterface $runner, OutputInterface $output): ResponseInterface
    {
        $output->write($runner->getCompletionMessage());
        $this->taskService->complete($runner->getTask());
        return new TaskCompletedResponse($runner->getCompletionMessage());
    }

}
