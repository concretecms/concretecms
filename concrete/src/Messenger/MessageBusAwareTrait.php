<?php

namespace Concrete\Core\Messenger;

use Symfony\Component\Messenger\MessageBusInterface;

/**
 * A trait used with MessageBusAwareInterface
 */
trait MessageBusAwareTrait
{

    protected $messageBus;

    public function setMessageBus(MessageBusInterface $messageBus)
    {
        $this->messageBus = $messageBus;
    }

}
