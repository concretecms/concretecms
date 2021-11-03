<?php
namespace Concrete\Core\Messenger\Transport\Sender;

use Psr\Container\ContainerInterface;

class SenderLocator implements ContainerInterface
{

    protected $senders = [];

    public function addSender(string $handle, callable $sender)
    {
        $this->senders[$handle] = $sender;
    }

    public function get($id)
    {
        $sender = $this->senders[$id];
        if (is_callable($sender)) {
            $sender = $sender();
        }
        return $sender;
    }

    public function has($id)
    {
        return array_key_exists($id, $this->senders);
    }


}