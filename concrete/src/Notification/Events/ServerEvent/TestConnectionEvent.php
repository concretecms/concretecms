<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

use Concrete\Core\Notification\Events\Topic\ConcreteTopic;
use Concrete\Core\Notification\Events\Topic\TestConnectionTopic;
use Concrete\Core\Notification\Events\Topic\TopicInterface;

class TestConnectionEvent extends AbstractConcreteEvent
{

    /**
     * @var string
     */
    protected $ping;

    public function __construct(string $ping)
    {
        $this->ping = $ping;
    }

    public function createTopic(): TopicInterface
    {
        return new TestConnectionTopic();
    }

    protected function getEventData()
    {
        return ['pong' => $this->ping];
    }

}

