<?php

namespace Concrete\Core\Foundation\Queue;

use Bernard\Message;
use Bernard\Producer;
use Bernard\Queue;
use Bernard\QueueFactory\PersistentFactory;
use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Events\EventDispatcher;
use Concrete\Core\Foundation\Queue\Mutex\MutexGeneratorFactory;
use Concrete\Core\Job\Job;
use Concrete\Core\Job\JobQueue;
use Concrete\Core\Job\QueueableJob;
use Concrete\Core\Support\Facade\Facade;
use Concrete\Core\System\Mutex\MutexBusyException;
use League\Tactician\Bernard\QueueableCommand;

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
     * @var PersistentFactory
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
        $this->factory = new PersistentFactory(
            $this->app->make('queue/driver'),
            $this->app->make('queue/serializer')
        );
        $this->producer = new Producer($this->factory, $this->app->make(EventDispatcher::class));
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
                if ($entry[2]) {
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
        return new JobQueue($job, $this->app, $this);
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

    private function getPollingMax(Queue $queue)
    {
        if (strpos((string) $queue, 'job_') === 0) {
            $job = Job::getByHandle(substr((string) $queue, 4));
            if ($job) {
                if ($job instanceof QueueableJob) {
                    $max = $job->getJobQueueBatchSize();
                }
            }
        }
        if (!isset($max)) {
            $max = $this->config->get(sprintf('app.queue.polling_batch.%s', (string) $queue));
            if (!$max) {
                $max = $this->config->get('app.queue.polling_batch.default');
            }
        }

        return $max;
    }
    public function consumeFromPoll(Queue $queue)
    {
        $max = $this->getPollingMax($queue);
        try {
            $this->consume($queue, [
                'stop-when-empty' => true,
                'max-messages' => $max
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
