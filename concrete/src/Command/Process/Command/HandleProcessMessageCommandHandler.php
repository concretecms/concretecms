<?php

namespace Concrete\Core\Command\Process\Command;

use Concrete\Core\Command\Process\ProcessUpdater;
use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Command\Task\Stamp\OutputStamp;
use Symfony\Component\Messenger\MessageBusInterface;

class HandleProcessMessageCommandHandler implements OutputAwareInterface
{

    use OutputAwareTrait;

    /**
     * @var MessageBusInterface
     */
    protected $messageBus;

    /**
     * @var ProcessUpdater
     */
    protected $processUpdater;

    public function __construct(MessageBusInterface $messageBus, ProcessUpdater $processUpdater)
    {
        $this->messageBus = $messageBus;
        $this->processUpdater = $processUpdater;
    }

    public function __invoke(HandleProcessMessageCommand $command)
    {
        $message = $command->getMessage();
        $stamps = [];
        if ($this->output) {
            $stamps = [new OutputStamp($this->output)];
        }
        try {
            $this->messageBus->dispatch($message, $stamps);
            $this->processUpdater->closeProcess($command->getProcess(), ProcessMessageInterface::EXIT_CODE_SUCCESS);
        } catch (\Exception $e) {
            $this->processUpdater->closeProcess(
                $command->getProcess(),
                ProcessMessageInterface::EXIT_CODE_FAILURE,
                $e->getMessage()
            );
            throw $e;
        }
    }


}
