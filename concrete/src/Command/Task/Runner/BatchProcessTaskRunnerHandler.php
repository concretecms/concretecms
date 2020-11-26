<?php
namespace Concrete\Core\Command\Task\Runner;

use Concrete\Core\Command\Batch\Command\HandleBatchMessageCommand;
use Concrete\Core\Entity\Command\Batch;

defined('C5_EXECUTE') or die("Access Denied.");

class BatchProcessTaskRunnerHandler extends ProcessTaskRunnerHandler
{

    /**
     * @param BatchProcessTaskRunner $runner
     */
    public function run(TaskRunnerInterface $runner)
    {
        $batch = $runner->getBatch();
        $process = $runner->getProcess();
        $batchEntity = $this->processFactory->createBatchEntity($batch);
        $process->setBatch($batchEntity);

        $total = 0;
        foreach ($batch->getWrappedMessages($batchEntity) as $message) {
            $this->messageBus->dispatch($message);
            $total++;
        }

        $this->processFactory->setBatchTotal($batchEntity, $process, $total);
    }

}
