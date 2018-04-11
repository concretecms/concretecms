<?php

namespace Concrete\Core\Job\Response;

use Concrete\Core\Foundation\Queue\Response\EnqueueItemsResponse;
use Concrete\Core\Job\JobQueue;
use Concrete\Core\Job\QueueableJob;

class EnqueueJobResponse extends EnqueueItemsResponse
{

    protected $queue;

    public function __construct(QueueableJob $job, $moreData = [])
    {
        $queue = $job->getQueueObject()->getQueue();
        parent::__construct($queue, $moreData);
    }



}
