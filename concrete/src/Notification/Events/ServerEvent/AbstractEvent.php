<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

use Concrete\Core\Notification\Events\Topic\TopicInterface;
use Symfony\Component\Mercure\Update;

abstract class AbstractEvent implements ServerEventInterface
{

    protected function isPrivate()
    {
        return false;
    }

    abstract protected function getEventData();

    abstract public function createTopic(): TopicInterface;

    public function getTopics(): array
    {
        $topic = $this->createTopic();
        return [$topic];
    }

    public function getData(): string
    {
        return json_encode($this->getEventData());
    }

    public function getUpdate(): Update
    {
        return new Update($this->getTopics(), $this->getData(), $this->isPrivate());
    }

}

