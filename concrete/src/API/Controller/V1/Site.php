<?php

namespace Concrete\Core\API\Controller\V1;

use Concrete\Core\API\Transformer\System\InfoTransformer;
use Concrete\Core\API\Transformer\System\Status\QueueStatusTransformer;
use Concrete\Core\Application\Application;
use Concrete\Core\Application\UserInterface\Sitemap\StandardSitemapProvider;
use Concrete\Core\Application\UserInterface\Sitemap\TreeCollection\TreeCollectionTransformer;
use Concrete\Core\System\Info;
use Concrete\Core\System\Status\QueueStatus;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class Site
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
     * /ccm/api/v1/site/trees
     *
     */
    public function trees(StandardSitemapProvider $provider, TreeCollectionTransformer $transformer)
    {
        // Extract the tree from the sitemap provider
        $tree = $provider->getTreeCollection();

        // Return a resource
        return $this->app->make(Item::class, [$tree, $transformer]);
    }

}
