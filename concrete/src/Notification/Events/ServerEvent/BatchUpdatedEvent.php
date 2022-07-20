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

    protected static function getEvent(): string
    {
        return 'BatchUpdated';
    }

    protected static function createTopic(string $slug): TopicInterface
    {
        return new ConcreteProcessTopic($slug);
    }

    protected function getEventData(): array
    {
        return ['batch' => $this->batch];
    }

}

