<?php

namespace Concrete\Core\Job;

use Config;
use Job as AbstractJob;
use Queue;
use ZendQueue\Message as ZendQueueMessage;
use ZendQueue\Queue as ZendQueue;

abstract class QueueableJob extends AbstractJob
{
    /** @var int The size of the batch */
    protected $jQueueBatchSize;

    /** @var ZendQueue */
    protected $jQueueObject;

    /**
     * Start processing a queue
     * Typically this is where you would inject new messages into the queue
     *
     * @param \ZendQueue\Queue $q
     * @return mixed
     */
    abstract public function start(ZendQueue $q);

    /**
     * Finish processing a queue
     *
     * @param \ZendQueue\Queue $q
     * @return mixed
     */
    abstract public function finish(ZendQueue $q);

    /**
     * Process a QueueMessage
     *
     * @param \ZendQueue\Message $msg
     * @return void
     */
    abstract public function processQueueItem(ZendQueueMessage $msg);

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
     * @return \ZendQueue\Queue
     */
    public function getQueueObject()
    {
        if ($this->jQueueObject === null) {
            $this->jQueueObject = Queue::get('job_' . $this->getJobHandle(), array('timeout' => 1));
        }

        return $this->jQueueObject;
    }

    /**
     * Delete the queue
     */
    public function reset()
    {
        parent::reset();
        $this->getQueueObject()->deleteQueue();
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
     * Mark the queue as having completed
     *
     * @param int $code 0 for success, otherwise the exception error code
     * @param bool $message The message to show
     * @return \Concrete\Core\Job\JobResult
     */
    public function markCompleted($code = 0, $message = false)
    {
        $obj = parent::markCompleted($code, $message);
        $queue = $this->getQueueObject();
        if (!$this->didFail()) {
            $queue->deleteQueue();
        }

        return $obj;
    }

    /**
     * Executejob for queueable jobs actually starts the queue, runs, and ends all in one function. This happens if we run a job in legacy mode.
     */
    public function executeJob()
    {
        // If the job's already running, don't try to restart it
        if ($this->getJobStatus() !== 'RUNNING') {
            $queue = $this->markStarted();

            // Prepare the queue for processing
            $this->start($queue);
        } else {
            $queue = $this->getQueueObject();
        }

        try {
            $batchSize = $this->getJobQueueBatchSize() ?: PHP_INT_MAX;

            // Loop over queue batches
            while (($messages = $queue->receive($batchSize)) && $messages->count() > 0) {
                // Run the batch
                $this->executeBatch($messages, $queue);
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

    /**
     * Process a queue batch
     *
     * @param array|iterator $batch
     * @param \ZendQueue\Queue $queue
     */
    public function executeBatch($batch, ZendQueue $queue)
    {
        foreach ($batch as $item) {
            $this->processQueueItem($item);
            $queue->deleteMessage($item);
        }
    }
}
