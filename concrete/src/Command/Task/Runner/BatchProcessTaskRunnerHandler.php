<?php
namespace Concrete\Core\Command\Task\Runner;

use Concrete\Core\Command\Batch\Command\HandleBatchMessageCommand;
use Concrete\Core\Command\Task\Output\OutputInterface;
use Concrete\Core\Command\Task\Stamp\OutputStamp;
use Concrete\Core\Entity\Command\Batch;

defined('C5_EXECUTE') or die("Access Denied.");

class BatchProcessTaskRunnerHandler extends ProcessTaskRunnerHandler
{

    /**
     * @param BatchProcessTaskRunner $runner
     */
    public function run(TaskRunnerInterface $runner, OutputInterface $output)
    {
        $batch = $runner->getBatch();
        $process = $runner->getProcess();
        $batchEntity = $this->processFactory->createBatchEntity($batch);
        $process->setBatch($batchEntity);

        $total = 0;
        foreach ($batch->getWrappedMessages($batchEntity) as $message) {
            $this->messageBus->dispatch($message, [new OutputStamp($output)]);
            $total++;
        }

        $this->processFactory->setBatchTotal($batchEntity, $process, $total);
    }

}
