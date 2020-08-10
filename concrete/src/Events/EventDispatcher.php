<?php

namespace Concrete\Core\Events;

use Concrete\Core\Events\Broadcast\BroadcastableEventInterface;
use Concrete\Core\Events\Broadcast\Broadcaster;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher as SymfonyEventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventDispatcher extends SymfonyEventDispatcher
{

    protected $broadcasters = [];

    /**
     * @var Broadcaster
     */
    protected $broadcaster;

    public function __construct(Broadcaster $broadcaster)
    {
        $this->broadcaster = $broadcaster;
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, Event $event = null)
    {
        if (null === $event) {
            $event = new Event();
        }

        if ($event instanceof BroadcastableEventInterface) {
            $this->broadcaster->broadcast($event->getBroadcastChannel(), $event);
        }

        if ($listeners = $this->getListeners($eventName)) {
            $this->doDispatch($listeners, $eventName, $event);
        }

        return $event;
    }


}
