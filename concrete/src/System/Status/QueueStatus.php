<?php
namespace Concrete\Core\System\Status;

use Bernard\Driver;
use Concrete\Core\API\Resource\TransformableInterface;
use Concrete\Core\API\Transformer\System\Status\QueueStatusTransformer;
use Concrete\Core\Application\Application;

class QueueStatus implements TransformableInterface
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

    public function getTransformer()
    {
        return new QueueStatusTransformer();
    }

    /**
     * @return array
     */
    public function getQueues()
    {
        return $this->queues;
    }




}
