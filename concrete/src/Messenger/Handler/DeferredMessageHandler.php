<?php

namespace Concrete\Core\Messenger\Handler;

use Concrete\Core\Command\Task\Output\OutputAwareInterface;
use Concrete\Core\Command\Task\Output\OutputAwareTrait;
use Concrete\Core\Command\Task\Stamp\OutputStamp;
use Symfony\Component\Messenger\MessageBusInterface;

class DeferredMessageHandler implements OutputAwareInterface
{

    use OutputAwareTrait;

    /**
     * @var MessageBusInterface
     */
    protected $messageBus;

    public function __construct(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

    public function __invoke($command)
    {
        $message = $command->getMessage();
        $stamps = [];
        if ($this->output) {
            $stamps = [new OutputStamp($this->output)];
        }
        return $this->messageBus->dispatch($message, $stamps);

    }


}
