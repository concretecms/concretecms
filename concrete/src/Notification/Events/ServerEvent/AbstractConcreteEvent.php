<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

use Concrete\Core\Notification\Events\Topic\ConcreteTopic;
use Concrete\Core\Notification\Events\Topic\TopicInterface;
use Symfony\Component\Mercure\Update;

/**
 * Used by all core events that want to interact with Mercure. Translates event names into
 * topics, etc...
 */
abstract class AbstractConcreteEvent implements ServerEventInterface
{

    abstract static protected function getEvent(): string;

    abstract protected function getEventData();

    protected static function createTopic(string $slug): TopicInterface
    {
        return new ConcreteTopic($slug);
    }

    protected function isPrivate()
    {
        return true;
    }

    public static function getTopics(): array
    {
        $slug = snake_case(static::getEvent());
        $topic = static::createTopic($slug);
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

