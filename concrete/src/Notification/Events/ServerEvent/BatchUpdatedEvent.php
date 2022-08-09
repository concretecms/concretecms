<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

use Concrete\Core\Entity\Command\Batch;
use Concrete\Core\Notification\Events\Topic\ConcreteProcessTopic;
use Concrete\Core\Notification\Events\Topic\TopicInterface;

class BatchUpdatedEvent extends AbstractConcreteEvent implements SubscribableEventInterface
{

    /**
     * @var Batch
     */
    protected $batch;

    /**
     * BatchUpdated constructor.
     * @param Batch $batch
     */
    public function __construct(Batch $batch)
    {
        $this->batch = $batch;
    }

    public function createTopic(): TopicInterface
    {
        return static::getTopicForSubscribing();
    }

    public static function getTopicForSubscribing(): TopicInterface
    {
        return new ConcreteProcessTopic('/batch_updated');
    }

    protected function getEventData(): array
    {
        return ['batch' => $this->batch];
    }

}

