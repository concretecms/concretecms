<?php
namespace Concrete\Core\System\Status;

use Concrete\Core\Application\Application;

class QueueStatus
{

    /**
     * @var Application
     */
    protected $app;

    protected $queues = [];

    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->loadQueues();
    }

    protected function loadQueues()
    {
        throw new \Exception('This has been removed temporarily while Symfony Messenger is being integrated.');
    }

    /**
     * @return array
     */
    public function getQueues()
    {
        return $this->queues;
    }




}
