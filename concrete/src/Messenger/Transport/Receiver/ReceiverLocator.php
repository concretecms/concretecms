<?php
namespace Concrete\Core\Messenger\Transport\Receiver;

use Psr\Container\ContainerInterface;

class ReceiverLocator implements ContainerInterface
{

    protected $receivers = [];

    public function has($id)
    {
        return array_key_exists($id, $this->receivers);
    }

    public function get($id)
    {
        $receiver = $this->receivers[$id];
        if (is_callable($receiver)) {
            $receiver = $receiver();
        }
        return $receiver;
    }

    public function getAll()
    {
        $receivers = [];
        foreach($this->receivers as $id => $value) {
            $receivers[] = $this->get($id);
        }
        return $receivers;
    }

    public function addReceiver(string $handle, callable $receiver)
    {
        $this->receivers[$handle] = $receiver;
    }
}