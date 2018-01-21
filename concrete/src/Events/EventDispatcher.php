<?php

namespace Concrete\Core\Events;

use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher as SymfonyEventDispatcher;
use Concrete\Core\Events\Broadcast\BroadcasterFactory;
use Concrete\Core\Events\Broadcast\BroadcastSerializer;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventDispatcher extends SymfonyEventDispatcher
{

    protected $broadcasters = [];

    protected $broadcasterFactory;

    public function __construct(BroadcasterFactory $broadcasterFactory)
    {
        $this->broadcasterFactory = $broadcasterFactory;
    }

    public function broadcast(array $events, BroadcastSerializer $serializer)
    {
        $broadcaster = $this->broadcasterFactory->createDriver();
        foreach($events as $event) {
            if (in_array($event, $this->broadcasters)) {
                continue; // don't do it twice.
            }

            $this->addListener($event, function($object) use ($event, $serializer, $broadcaster) {
                $json = $serializer->serializeForBroadcast($object);
                $broadcaster->broadcast($event, $json);
            });
        }
    }

}
