<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

use Concrete\Core\Notification\Events\Topic\ConcreteProcessTopic;
use Concrete\Core\Notification\Events\Topic\TopicInterface;

class ProcessOutputEvent extends AbstractConcreteEvent implements SubscribableEventInterface
{

    /**
     * @var string
     */
    protected $processId;

    /**
     * @var string
     */
    protected $message;

    public function __construct(string $processId, string $message)
    {
        $this->processId = $processId;
        $this->message = $message;
    }

    public function createTopic(): TopicInterface
    {
        return static::getTopicForSubscribing();
    }

    public static function getTopicForSubscribing(): TopicInterface
    {
        return new ConcreteProcessTopic('/process_output');
    }

    protected function getEventData(): array
    {
        return [
            'processId' => $this->processId,
            'message' => $this->message
        ];
    }

}

