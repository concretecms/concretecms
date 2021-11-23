<?php

namespace Concrete\Core\Events;

use Symfony\Contracts\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventDispatcher as SymfonyEventDispatcher;

class EventDispatcher
{

    /**
     * @var SymfonyEventDispatcher
     */
    protected $eventDispatcher;

    public function __construct(SymfonyEventDispatcher $eventDispatcher)
    {
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

        return $this->eventDispatcher->dispatch($event, $eventName);
    }

    public function __call($name, $arguments)
    {
        return $this->eventDispatcher->$name(...$arguments);
    }

    /**
     * @return SymfonyEventDispatcher
     */
    public function getEventDispatcher(): SymfonyEventDispatcher
    {
        return $this->eventDispatcher;
    }



}
