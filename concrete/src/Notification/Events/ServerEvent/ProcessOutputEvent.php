<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

use Concrete\Core\Notification\Events\Topic\ConcreteProcessTopic;
use Concrete\Core\Notification\Events\Topic\TopicInterface;

class ProcessOutputEvent extends AbstractConcreteEvent
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

    protected static function getEvent(): string
    {
        return 'ProcessOutput';
    }

    protected static function createTopic(string $slug): TopicInterface
    {
        return new ConcreteProcessTopic($slug);
    }

    protected function getEventData(): array
    {
        return [
            'processId' => $this->processId,
            'message' => $this->message
        ];
    }

}

