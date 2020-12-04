<?php

namespace Concrete\Core\Notification\Mercure\Update;

use Concrete\Core\Entity\Command\Batch;
use Concrete\Core\Entity\Command\Process;

class BatchUpdated implements UpdateInterface
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

    public function getTopicURL(): string
    {
        return 'https://global.concretecms.com/batches/' . $this->batch->getId();
    }

    public function getData(): array
    {
        return ['batch' => $this->batch];
    }

}

