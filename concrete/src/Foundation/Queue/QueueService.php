<?php

namespace Concrete\Core\Foundation\Queue;

use Bernard\Message;
use Bernard\Producer;
use Bernard\Queue;
use Bernard\Queue\RoundRobinQueue;
use Bernard\QueueFactory\PersistentFactory;
use Concrete\Core\Application\Application;
use Concrete\Core\Events\EventDispatcher;

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
     * @var Producer
     */
    protected $producer;

    public function __construct(Application $app)
    {
        $this->app = $app;
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
    public function get($queue)
    {

        if (is_array($queue) && count($queue) > 1) {
            $queues = array_map([$this->factory, 'create'], $queue);
            return new RoundRobinQueue($queues);
        }

        if (is_array($queue)) {
            $queue = $queue[0];
        }

        return $this->factory->create($queue);
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

}
