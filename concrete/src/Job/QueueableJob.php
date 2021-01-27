<?php

namespace Concrete\Core\Job;

use Config;
use Job as AbstractJob;

abstract class QueueableJob extends AbstractJob
{
    /** @var int The size of the batch */
    protected $jQueueBatchSize;

    /**
     * @var JobQueue
     */
    protected $jQueueObject;

    /**
     * Start processing a queue
     * Typically this is where you would inject new messages into the queue
     *
     * @return mixed
     */
    abstract public function start(JobQueue $q);

    /**
     * Finish processing a queue
     *
     * @return mixed
     */
    abstract public function finish(JobQueue $q);

    /**
     * Process a QueueMessage
     *
     * @param \ZendQueue\Message $msg
     * @return void
     */
    abstract public function processQueueItem(JobQueueMessage $msg);

    /**
     * QueueableJob constructor.
     * This is here and empty since it'd be a BC break to remove it.
     */
    public function __construct()
    {
    }

    /**
     * This is disabled since we don't want users to accidentally run a queable job without knowing it
     */
    public function run()
    {
    }

    /**
     * Get the size of the queue batches
     * @return int
     */
    public function getJobQueueBatchSize()
    {
        // If there's no batch size set, let's pull the batch size from the config
        if ($this->jQueueBatchSize === null) {
            $this->jQueueBatchSize = Config::get('concrete.limits.job_queue_batch');
        }

        return $this->jQueueBatchSize;
    }

    /**
     * Get the queue object we're going to use to queue
     * @return JobQueue
     */
    public function getQueueObject()
    {
        if ($this->jQueueObject === null) {
            $this->jQueueObject = new JobQueue($this);
        }

        return $this->jQueueObject;
    }

    /**
     * Mark the queue as started
     */
    public function markStarted()
    {
        parent::markStarted();
        return $this->getQueueObject();
    }

    /**
     * Executejob for queueable jobs actually starts the queue, runs, and ends all in one function. This happens if we run a job in legacy mode.
     */
    public function executeJob()
    {
        try {
            if ($this->getJobStatus() !== 'RUNNING') {
                $queue = $this->markStarted();
                $this->start($queue);
            } else {
                $queue = $this->getQueueObject();
            }

            // Mark the queue as finished
            $output = $this->finish($queue);

            // Mark the job as completed
            $result = $this->markCompleted(0, $output);
        } catch (\Exception $e) {
            $result = $this->markCompleted(Job::JOB_ERROR_EXCEPTION_GENERAL, $e->getMessage());
            $result->message = $result->result; // needed for progressive library.
        }

        return $result;
    }
}
