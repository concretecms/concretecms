<?php
namespace Concrete\Core\Messenger;

use Symfony\Component\Messenger\MessageBusInterface;

class MessageBusManager
{

    const BUS_DEFAULT = 'command';
    const BUS_DEFAULT_ASYNC = 'async';

    protected $buses = [];

    public function getBus(string $handle):? MessageBusInterface
    {
        $bus = $this->buses[$handle];
        if (!$bus) {
            throw new \RuntimeException(t('Unable to locate bus by handle: [%s]', $handle));
        }

        if (is_callable($bus)) {
            $bus = $bus();
            $this->buses[$handle] = $bus;
        }

        return $bus;
    }

    public function addBus(string $handle, callable $bus)
    {
        $this->buses[$handle] = $bus;
    }

}