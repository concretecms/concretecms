<?php
namespace Concrete\Core\Messenger;

use Psr\Container\ContainerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class MessageBusManager implements ContainerInterface
{

    const BUS_DEFAULT = 'command';

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

    public function has($id)
    {
        return array_key_exists($id, $this->buses);
    }

    public function get($id)
    {
        return $this->getBus($id);
    }

}