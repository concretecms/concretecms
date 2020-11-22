<?php

namespace Concrete\Core\Command\Process\Command;

use Symfony\Component\Messenger\MessageBusInterface;

class HandleProcessMessageCommandHandler
{

    /**
     * @var MessageBusInterface
     */
    protected $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke(HandleProcessMessageCommand $command)
    {
        $message = $command->getMessage();
        $envelope = $this->messageBus->dispatch($message);

        return $envelope;

    }


}
