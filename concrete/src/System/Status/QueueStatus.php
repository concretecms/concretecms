<?php

namespace Concrete\Core\System\Status;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Queue\QueueService;

class QueueStatus
{

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var QueueService
     */
    protected $queueService;

    protected $queues;

    public function __construct(QueueService $queueService, Application $app)
    {
        $this->app = $app;
        $this->queueService = $queueService;
    }

    /**
     * Load in active queues
     */
    protected function loadQueues()
    {
        $queues = $this->queueService->listQueues();

        if (is_array($queues)) {
            foreach ($queues as $queue => $count) {
                $this->queues[] = new QueueStatusQueue($queue, $count);
            }
        }
    }

    /**
     * @return array
     */
    public function getQueues()
    {
        if ($this->queues === null) {
            $this->loadQueues();
        }

        return $this->queues;
    }
}
