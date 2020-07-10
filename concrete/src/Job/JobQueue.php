<?php
namespace Concrete\Core\Job;

use Bernard\Queue;
use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Queue\Batch;
use Concrete\Core\Foundation\Command\AsynchronousBus;
use Concrete\Core\Foundation\Queue\Batch\BatchFactory;
use Concrete\Core\Foundation\Queue\Batch\BatchProgressUpdater;
use Concrete\Core\Foundation\Queue\QueueService;
use Concrete\Core\Job\Command\ExecuteJobItemCommand;
use Doctrine\ORM\EntityManager;

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

    /**
     * @var int
     */
    protected $totalMessages = 0;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var BatchProgressUpdater
     */
    protected $batchProgressUpdater;

    public function __construct(QueueableJob $job, Application $app, QueueService $service, BatchProgressUpdater $batchProgressUpdater, BatchFactory $batchFactory, EntityManager $em)
    {
        $this->service = $service;
        $this->batchFactory = $batchFactory;
        $this->batchProgressUpdater = $batchProgressUpdater;
        $this->job = $job;
        $this->entityManager = $em;
        $this->app = $app;
        $this->queue = $service->get($service->getDefaultQueueHandle());

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
        return $this->batchFactory->createOrGetBatch(sprintf('job_%s', $this->job->getJobHandle()));
    }

    public function saveBatch()
    {
        $this->batchProgressUpdater->incrementTotals($this->getBatch(), $this->totalMessages);
    }

    public function deleteQueue()
    {
        $this->queue->close();
        $this->entityManager->remove($this->batch);
        $this->entityManager->flush();
    }

    public function send($mixed)
    {
        $data = serialize($mixed);
        $this->totalMessages++;
        $command = new ExecuteJobItemCommand($this->getBatch()->getBatchHandle(), $this->job->getJobHandle(), $data);
        return $this->app->getCommandDispatcher()->dispatch($command, AsynchronousBus::getHandle());
    }

}