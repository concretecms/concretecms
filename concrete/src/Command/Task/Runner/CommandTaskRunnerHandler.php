<?php
namespace Concrete\Core\Command\Task\Runner;

use Concrete\Core\Command\Task\Output\OutputInterface;
use Concrete\Core\Command\Task\Runner\Context\ContextInterface;
use Concrete\Core\Command\Task\Runner\Response\ResponseInterface;
use Concrete\Core\Command\Task\Runner\Response\TaskCompletedResponse;
use Concrete\Core\Command\Task\Stamp\OutputStamp;
use Concrete\Core\Command\Task\TaskService;
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

    public function start(TaskRunnerInterface $runner, ContextInterface $context)
    {
        // Nothing.
    }

    public function run(TaskRunnerInterface $runner, ContextInterface $context)
    {
        $message = $runner->getCommand();
        $context->dispatchCommand($message);
    }

    public function complete(TaskRunnerInterface $runner, ContextInterface $context): ResponseInterface
    {
        $output = $context->getOutput();
        $output->write($runner->getCompletionMessage());
        $this->taskService->complete($runner->getTask());
        return new TaskCompletedResponse($runner->getCompletionMessage());
    }

}
