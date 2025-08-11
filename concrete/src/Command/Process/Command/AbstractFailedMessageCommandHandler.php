<?php

namespace Concrete\Core\Command\Process\Command;

use Concrete\Core\Messenger\Transport\FailedTransportManager;
use Symfony\Component\Messenger\Transport\Receiver\ReceiverInterface;

abstract class AbstractFailedMessageCommandHandler
{

    /**
     * @var FailedTransportManager
     */
    protected $failedTransportManager;

    public function __construct(FailedTransportManager $failedTransportManager)
    {
        $this->failedTransportManager = $failedTransportManager;
    }

    protected function getReceiverFromCommand($command): ReceiverInterface
    {
        $receiverName = $command->getReceiverName() ?: $this->failedTransportManager->getDefaultFailedReceiverName();
        $receiver = $this->failedTransportManager->getReceivers()->get($receiverName);
        return $receiver;
    }

}