<?php

namespace Concrete\Core\Foundation\Queue\Receiver;

use Bernard\Message;
use League\Tactician\Bernard\QueueCommand;
use League\Tactician\Bernard\Receiver;

/**
 * Receives a Message from a Consumer and handles it. Checks to see if the message is a QueueCommand; if it is,
 * we are wrapping the real command that we want to send to the bus.
 */
final class QueueCommandMessageReceiver extends Receiver
{
    /**
     * {@inheritdoc}
     */
    public function handle(Message $message)
    {
        if ($message instanceof QueueCommand) {
            return $this->commandBus->handle($message->getCommand());
        }

        return $this->commandBus->handle($message);
    }
}
