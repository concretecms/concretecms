<?php
namespace Concrete\Core\Job;

use Bernard\Queue;
use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Queue\Batch;
use Concrete\Core\Foundation\Queue\Batch\BatchFactory;
use Concrete\Core\Foundation\Queue\QueueService;
use Concrete\Core\Job\Command\ExecuteJobItemCommand;

/**
 * Wrapper class for our batching specifically for use with jobs to minimize backward compatibility headaches.
 */
class JobQueue
{
    /**
     * @var BatchFactory
     */
    protected $batchFactory;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var QueueableJob
     */
    protected $job;

    /**
     * @var QueueService
     */
    protected $service;

    /**
     * @var Batch
     */
    protected $batch;

    public function __construct(QueueableJob $job, Application $app, QueueService $service, BatchFactory $batchFactory)
    {
        $this->service = $service;
        $this->batchFactory = $batchFactory;
        $this->job = $job;
        $this->app = $app;
        $this->queue = $service->get($service->getDefaultQueueHandle());
        $this->batch = $this->batchFactory->getBatch(sprintf('job_%s', $this->job->getJobHandle()));

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

    public function getBatch()
    {
        return $this->batch;
    }

    public function send($mixed)
    {
        $data = serialize($mixed);
        $command = new ExecuteJobItemCommand($this->batch->getBatchHandle(), $this->job->getJobHandle(), $data);
        return $this->app->getCommandDispatcher()->dispatchOnQueue($command);
    }

}