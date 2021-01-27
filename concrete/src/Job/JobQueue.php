<?php
namespace Concrete\Core\Job;

/**
 * Wrapper class for our batching specifically for use with jobs to minimize backward compatibility headaches.
 */
class JobQueue
{

    /**
     * @var QueueableJob
     */
    protected $job;

    public function __construct(QueueableJob $job)
    {
        $this->job = $job;
    }

    public function send($mixed)
    {
        $message = new JobQueueMessage($mixed);
        $this->job->processQueueItem($message);
    }

}