<?php

namespace Concrete\Core\Events;

use Concrete\Core\Events\Broadcast\BroadcastableEventInterface;
use Concrete\Core\Events\Broadcast\Broadcaster;
use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher as SymfonyEventDispatcher;

class EventDispatcher
{

    protected $broadcasters = [];

    /**
     * @var Broadcaster
     *
     */
    protected $broadcaster;

    /**
     * @var SymfonyEventDispatcher
     */
    protected $eventDispatcher;

    public function __construct(Broadcaster $broadcaster, SymfonyEventDispatcher $eventDispatcher)
    {
        $this->broadcaster = $broadcaster;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, object $event = null)
    {
        if (null === $event) {
            $event = new Event();
        }

        if ($event instanceof BroadcastableEventInterface) {
            $this->broadcaster->broadcast($event->getBroadcastChannel(), $event);
        }

        return $this->eventDispatcher->dispatch($event, $eventName);
    }

    public function __call($name, $arguments)
    {
        return $this->eventDispatcher->$name(...$arguments);
    }


}
