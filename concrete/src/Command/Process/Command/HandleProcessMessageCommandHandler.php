<?php

namespace Concrete\Core\Command\Process\Command;

use Concrete\Core\Command\Process\ProcessUpdater;
use Symfony\Component\Messenger\MessageBusInterface;

class HandleProcessMessageCommandHandler
{

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
        $envelope = $this->messageBus->dispatch($message);

        $this->processUpdater->closeProcess($command->getProcess());

        return $envelope;

    }


}
