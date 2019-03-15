<?php

namespace Concrete\Core\Foundation\Queue\Receiver;

use Bernard\Message;
use Concrete\Core\Foundation\Command\Dispatcher;
use League\Tactician\Bernard\QueueCommand;
use Concrete\Core\Foundation\Command\SynchronousBus;

/**
 * Receives a Message from a Consumer and handles it. Checks to see if the message is a QueueCommand; if it is,
 * we are wrapping the real command that we want to send to the bus.
 */
final class QueueCommandMessageReceiver
{

    protected $dispatcher;

    public function __construct(Dispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function handle(Message $message)
    {
        $command = $message;
        if ($message instanceof QueueCommand) {
            $command = $message->getCommand();
        }
        return $this->dispatcher->dispatch($command, SynchronousBus::getHandle());
    }

    /**
     * Makes the receiver callable to be able to register it in a router
     *
     * @param Message $message
     *
     * @return mixed
     */
    public function __invoke(Message $message)
    {
        return $this->handle($message);
    }

}
