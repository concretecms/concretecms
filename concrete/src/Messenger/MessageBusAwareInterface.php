<?php
namespace Concrete\Core\Messenger;

use Symfony\Component\Messenger\MessageBusInterface;

/**
 * This interface declares awareness of the message bus.
 */
interface MessageBusAwareInterface
{

    public function setMessageBus(MessageBusInterface $messageBus);

}
