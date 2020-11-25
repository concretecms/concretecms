<?php
namespace Concrete\Core\Command\Task\Runner;

use Concrete\Core\Command\Process\Command\HandleProcessMessageCommand;
use Concrete\Core\Command\Process\ProcessFactory;
use Concrete\Core\Command\Task\TaskService;
use Symfony\Component\Messenger\MessageBusInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class BatchProcessTaskRunnerHandler
{

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

    public function __invoke(BatchProcessTaskRunner $runner)
    {
        $this->taskService->start($runner->getTask());
        $process = $this->processFactory->createWithBatch($runner->getBatch(), $runner->getTask(), $runner->getInput());
        $runner->setProcess($process);
    }


}
