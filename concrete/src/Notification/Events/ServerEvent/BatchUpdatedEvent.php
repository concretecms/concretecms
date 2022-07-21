<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

use Concrete\Core\Entity\Command\Batch;
use Concrete\Core\Notification\Events\Topic\ConcreteProcessTopic;
use Concrete\Core\Notification\Events\Topic\TopicInterface;

class BatchUpdatedEvent extends AbstractConcreteEvent
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
        return new ConcreteProcessTopic('/batch_updated');
    }

    protected function getEventData(): array
    {
        return ['batch' => $this->batch];
    }

}

