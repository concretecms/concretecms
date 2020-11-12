<?php
namespace Concrete\Core\Command\Task;

use Concrete\Core\Application\Application;
use Concrete\Core\Command\Task\Controller\ClearCacheController;
use Concrete\Core\Command\Task\Controller\GenerateSitemapController;
use Concrete\Core\Command\Task\Controller\RescanFilesController;
use Concrete\Core\Support\Manager as CoreManager;

defined('C5_EXECUTE') or die("Access Denied.");

class Manager extends CoreManager
{
    public function createRescanFilesDriver()
    {
        return $this->app->make(RescanFilesController::class);
    }

    public function createClearCacheDriver()
    {
        return $this->app->make(ClearCacheController::class);
    }

    public function createGenerateSItemapDriver()
    {
        return $this->app->make(GenerateSitemapController::class);
    }

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }


}
