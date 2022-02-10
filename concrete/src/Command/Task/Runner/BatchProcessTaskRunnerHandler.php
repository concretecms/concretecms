<?php
namespace Concrete\Core\Command\Task\Runner;

use Concrete\Core\Command\Batch\Stamp\BatchStamp;
use Concrete\Core\Command\Process\Command\ProcessMessageInterface;
use Concrete\Core\Command\Process\ProcessFactory;
use Concrete\Core\Command\Process\ProcessUpdater;
use Concrete\Core\Command\Task\Output\OutputInterface;
use Concrete\Core\Command\Task\Runner\Context\ContextInterface;
use Concrete\Core\Command\Task\Stamp\OutputStamp;
use Concrete\Core\Command\Task\TaskService;

defined('C5_EXECUTE') or die("Access Denied.");

class BatchProcessTaskRunnerHandler extends ProcessTaskRunnerHandler
{

    /**
     * @var ProcessUpdater
     */
    protected $processUpdater;

    public function __construct(ProcessUpdater $processUpdater, TaskService $taskService, ProcessFactory $processFactory)
    {
        $this->processUpdater = $processUpdater;
        parent::__construct($taskService, $processFactory);
    }

    /**
     * @param BatchProcessTaskRunner $runner
     */
    public function run(TaskRunnerInterface $runner, ContextInterface $context)
    {
        $batch = $runner->getBatch();
        $process = $runner->getProcess();
        $batchEntity = $this->processFactory->createBatchEntity($batch);
        $process->setBatch($batchEntity);

        $messages = $batch->getWrappedMessages($batchEntity);
        $total = count($messages);
        $this->processFactory->setBatchTotal($batchEntity, $process, $total);

        if ($total > 0) {
            foreach ($messages as $message) {
                $context->dispatchCommand($message, [new BatchStamp($batchEntity->getId())]);
            }
        } else {
            // There were no actual messages in this batch – so let's close it
            $this->processUpdater->closeProcess($process, ProcessMessageInterface::EXIT_CODE_SUCCESS);
        }
    }

}
