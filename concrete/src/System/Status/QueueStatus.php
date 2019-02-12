<?php
namespace Concrete\Core\System\Status;

use Bernard\Driver;
use Concrete\Core\Application\Application;

class QueueStatus
{

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Driver
     */
    protected $driver;

    protected $queues = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->driver = $this->app->make('queue/driver');
        $this->loadQueues();
    }

    protected function loadQueues()
    {
        $queues = $this->driver->listQueues();
        foreach($queues as $queue) {
            $count = $this->driver->countMessages($queue);
            $qq = new QueueStatusQueue($queue, $count);
            $this->queues[] = $qq;
        }
    }

    /**
     * @return array
     */
    public function getQueues()
    {
        return $this->queues;
    }




}
