<?php

namespace Concrete\Core\API\Controller\V1;

use Concrete\Core\API\Transformer\System\InfoTransformer;
use Concrete\Core\API\Transformer\System\Status\QueueStatusTransformer;
use Concrete\Core\Application\Application;
use Concrete\Core\System\Info;
use Concrete\Core\System\Status\QueueStatus;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class System
{

    /**
     * @var \League\Fractal\Manager
     */
    private $manager;

    /**
     * @var \Concrete\Core\Application\Application
     */
    private $app;

    public function __construct(Manager $manager, Application $app)
    {
        $this->manager = $manager;
        $this->app = $app;
    }

    /**
     * Route handler that returns system information
     * /ccm/api/v1/system/info
     *
     * @param \Concrete\Core\System\Info $info
     * @param \Concrete\Core\API\Transformer\System\InfoTransformer $transformer
     * @return \League\Fractal\Resource\Item
     */
    public function info(Info $info, InfoTransformer $transformer)
    {
        // Return a resource
        return $this->app->make(Item::class, [$info, $transformer]);
    }

    /**
     * Queue status route handler
     * /ccm/api/v1/system/status/queue
     *
     * @param \Concrete\Core\System\Status\QueueStatus $status
     * @param \Concrete\Core\API\Transformer\System\Status\QueueStatusTransformer $transformer
     * @return \League\Fractal\Resource\Item|mixed
     */
    public function queueStatus(QueueStatus $status, QueueStatusTransformer $transformer)
    {
        // Return our queue status resource
        return $this->app->make(Item::class, [$status, $transformer]);
    }

}
