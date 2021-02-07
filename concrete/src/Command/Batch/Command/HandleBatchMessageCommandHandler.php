<?php

namespace Concrete\Core\Command\Batch\Command;

use Concrete\Core\Command\Batch\BatchUpdater;
use Concrete\Core\Command\Process\Command\ProcessMessageInterface;
use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Command\Task\Stamp\OutputStamp;
use Symfony\Component\Messenger\MessageBusInterface;

class HandleBatchMessageCommandHandler implements OutputAwareInterface
{

    use OutputAwareTrait;

    /**
     * @var MessageBusInterface
     */
    protected $messageBus;

    /**
     * @var BatchUpdater
     */
    protected $batchUpdater;

    public function __construct(MessageBusInterface $messageBus, BatchUpdater $batchUpdater)
    {
        $this->messageBus = $messageBus;
        $this->batchUpdater = $batchUpdater;
    }

    public function __invoke(HandleBatchMessageCommand $command)
    {
        $message = $command->getMessage();
        $stamps = [];
        if ($this->output) {
            $stamps = [new OutputStamp($this->output)];
        }
        try {
            $this->messageBus->dispatch($message, $stamps);
            $this->batchUpdater->updateJobs($command->getBatch(), BatchUpdater::COLUMN_PENDING, -1);
            $this->batchUpdater->checkBatchProcessForClose(
                $command->getBatch(),
                ProcessMessageInterface::EXIT_CODE_SUCCESS
            );
        } catch (\Exception $e) {
            $this->batchUpdater->updateJobs($command->getBatch(), BatchUpdater::COLUMN_PENDING, -1);
            $this->batchUpdater->updateJobs($command->getBatch(), BatchUpdater::COLUMN_FAILED, 1);
            $this->batchUpdater->checkBatchProcessForClose(
                $command->getBatch(),
                ProcessMessageInterface::EXIT_CODE_FAILURE,
                $e->getMessage()
            );
            throw $e;
        }
    }


}
