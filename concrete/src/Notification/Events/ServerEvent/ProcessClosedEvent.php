<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

use Concrete\Core\Notification\Events\Topic\ConcreteProcessTopic;
use Concrete\Core\Notification\Events\Topic\TopicInterface;

class ProcessClosedEvent extends AbstractConcreteEvent implements SubscribableEventInterface
{

    /**
     * @var array
     */
    protected $processData;

    /**
     * @var integer
     */
    protected $exitCode;

    /**
     * ProcessClosed constructor.
     * @param array $processData
     * @param int $exitCode
     */
    public function __construct(array $processData, int $exitCode)
    {
        $this->processData = $processData;
        $this->exitCode = $exitCode;
    }

    public function createTopic(): TopicInterface
    {
        return static::getTopicForSubscribing();
    }

    public static function getTopicForSubscribing(): TopicInterface
    {
        return new ConcreteProcessTopic('/close_process');
    }

    protected function getEventData(): array
    {
        return [
            'process' => $this->processData,
            'exitCode' => $this->exitCode,
        ];
    }

}

