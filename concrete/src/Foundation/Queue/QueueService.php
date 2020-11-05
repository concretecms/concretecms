<?php

namespace Concrete\Core\Foundation\Queue;

use Bernard\Message;
use Bernard\Producer;
use Bernard\Queue;
use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Queue\Batch;
use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\Foundation\Queue\Batch\BatchFactory;
use Concrete\Core\Foundation\Queue\Mutex\MutexGeneratorFactory;
use Concrete\Core\Job\Job;
use Concrete\Core\Job\JobQueue;
use Concrete\Core\Job\QueueableJob;
use Concrete\Core\System\Mutex\MutexBusyException;
use Doctrine\ORM\EntityManager;

/**
 * A handy wrapper for calling Bernard functions using the full API.
 * Class QueueService
 */
class QueueService
{

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var QueueFactory
     */
    protected $factory;

    /**
     * @var Repository
     */
    protected $config;

    /**
     * @var Producer
     */
    protected $producer;

    /**
     * @var MutexGeneratorFactory
     */
    protected $mutexGeneratorFactory;

    public function __construct(Application $app, Repository $config, MutexGeneratorFactory $mutexGeneratorFactory)
    {
        $this->app = $app;
        $this->config = $config;
        $this->mutexGeneratorFactory = $mutexGeneratorFactory;
        $this->factory = new QueueFactory(
            $this->app->make('queue/driver'),
            $this->app->make('queue/serializer')
        );
        $this->producer = new Producer($this->factory, $this->app->make(EventDispatcher::class));
    }


    /**
     * @return string
     */
    public function getDefaultQueueHandle()
    {
        return $this->config->get('concrete.queue.default');
    }

    /**
     * A single queue (string) or an array of queues.
     * @param $mixed $queue
     */
    public function get($queue = null)
    {

        if (!$queue) {
            $queue = [];
            foreach($this->config->get('app.commands') as $entry) {
                if ($entry[2] ?? null) {
                    $queue[] = $entry[2];
                }
            }
        }

        if (is_array($queue) && count($queue) > 1) {
            $queues = array_map([$this->factory, 'create'], $queue);
            return new RoundRobinQueue($queues);
        }

        if (is_array($queue)) {
            $queue = $queue[0];
        }

        return $this->factory->create($queue);
    }

    public function getJobQueue(QueueableJob $job)
    {
        return $this->app->make(JobQueue::class, ['job' => $job]);
    }

    /**
     * Sends an arbitrary object into the queue.
     * @param Queue $queue
     * @param $mixed
     */
    public function push(Queue $queue, $mixed)
    {
        if (!($mixed instanceof Message)) {
            $mixed = new Message\PlainMessage((string) $queue, $mixed);
        }
        $this->producer->produce($mixed);
    }

    /**
     * @deprecated
     * @param Queue $queue
     * @param $mixed
     */
    public function send(Queue $queue, $mixed)
    {
        $this->push($queue, $mixed);
    }

    private function getPollingMax(string $batch)
    {
        if (strpos($batch, 'job_') === 0) {
            $job = Job::getByHandle(substr($batch, 4));
            if ($job) {
                if ($job instanceof QueueableJob) {
                    $max = $job->getJobQueueBatchSize();
                }
            }
        }
        if (!isset($max)) {
            $max = $this->config->get(sprintf('concrete.queue.polling_batch.%s', $batch));
            if (!$max) {
                $max = $this->config->get('concrete.queue.polling_batch.default');
            }
        }

        return $max;
    }
    public function consumeBatchFromPoll(Batch $batch)
    {
        $maxMessages = $this->getPollingMax($batch->getBatchHandle());
        $queue = $this->get($this->getDefaultQueueHandle());
        try {
            $this->consume($queue, [
                'stop-when-empty' => true,
                'max-messages' => $maxMessages
            ]);
        } catch (MutexBusyException $exception) {
            return false;
        }
    }

    public function consume(Queue $queue, $options = [])
    {
        $consumer = $this->app->make('queue/consumer');
        $generator = $this->mutexGeneratorFactory->create($queue);
        $generator->execute($queue, function() use ($consumer, $queue, $options) {
            $consumer->consume($queue, $options);
        });
    }


}
