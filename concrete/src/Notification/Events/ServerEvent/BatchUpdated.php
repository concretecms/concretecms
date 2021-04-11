<?php

namespace Concrete\Core\Notification\Events\ServerEvent;

use Concrete\Core\Entity\Command\Batch;

class BatchUpdated implements EventInterface
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

    public function getEvent(): string
    {
        return 'BatchUpdated';
    }

    public function getData(): array
    {
        return ['batch' => $this->batch];
    }

}

