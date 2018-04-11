<?php
namespace Concrete\Core\Job;

use Bernard\Queue;
use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Queue\QueueService;
use Concrete\Core\Job\Command\ExecuteJobItemCommand;

/**
 * Wrapper class for Bernard specifically for use with jobs to minimize backward compatibility headaches.
 * Class JobQueue
 * @package Concrete\Core\Job
 */
class JobQueue
{
    /**
     * @var Queue
     */
    protected $queue;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var QueueableJob
     */
    protected $job;

    public function __construct(QueueableJob $job, Application $app, QueueService $queueService)
    {
        $queue = $queueService->get(sprintf('job_%s', $job->getJobHandle()));
        $this->queue = $queue;
        $this->job = $job;
        $this->app = $app;
    }

    /**
     * @return Queue
     */
    public function getQueue()
    {
        return $this->queue;
    }

    public function close()
    {
        return $this->queue->close();
    }

    public function send($mixed)
    {
        $data = serialize($mixed);
        $command = new ExecuteJobItemCommand((string) $this->queue, $this->job->getJobHandle(), $data);
        return $this->app->queueCommand($command);
    }

}