<?php
namespace Concrete\Core\Command\Task;

use Concrete\Core\Application\Application;
use Concrete\Core\Command\Task\Controller\CheckAutomatedGroupsController;
use Concrete\Core\Command\Task\Controller\ClearCacheController;
use Concrete\Core\Command\Task\Controller\DeactivateUsersController;
use Concrete\Core\Command\Task\Controller\GenerateSitemapController;
use Concrete\Core\Command\Task\Controller\GenerateThumbnailsController;
use Concrete\Core\Command\Task\Controller\ProcessEmailController;
use Concrete\Core\Command\Task\Controller\ReindexContentController;
use Concrete\Core\Command\Task\Controller\RemoveUnvalidatedUsersController;
use Concrete\Core\Command\Task\Controller\RemoveOldFileAttachmentsController;
use Concrete\Core\Command\Task\Controller\RemoveOldPageVersionsController;
use Concrete\Core\Command\Task\Controller\RescanFilesController;
use Concrete\Core\Command\Task\Controller\UpdateStatisticsController;
use Concrete\Core\Support\Manager as CoreManager;

defined('C5_EXECUTE') or die("Access Denied.");

class Manager extends CoreManager
{
    public function createRescanFilesDriver()
    {
        return $this->container->make(RescanFilesController::class);
    }

    public function createClearCacheDriver()
    {
        return $this->container->make(ClearCacheController::class);
    }

    public function createGenerateSitemapDriver()
    {
        return $this->container->make(GenerateSitemapController::class);
    }

    public function createCheckAutomatedGroupsDriver()
    {
        return $this->container->make(CheckAutomatedGroupsController::class);
    }

    public function createDeactivateUsersDriver()
    {
        return $this->container->make(DeactivateUsersController::class);
    }

    public function createGenerateThumbnailsDriver()
    {
        return $this->container->make(GenerateThumbnailsController::class);
    }

    public function createUpdateStatisticsDriver()
    {
        return $this->container->make(UpdateStatisticsController::class);
    }

    public function createRemoveOldPageVersionsDriver()
    {
        return $this->container->make(RemoveOldPageVersionsController::class);
    }

    public function createReindexContentDriver()
    {
        return $this->container->make(ReindexContentController::class);
    }

    public function createProcessEmailDriver()
    {
        return $this->container->make(ProcessEmailController::class);
    }

    public function createRemoveOldFileAttachmentsDriver()
    {
        return $this->container->make(RemoveOldFileAttachmentsController::class);
    }

    public function createRemoveUnvalidatedUsersDriver()
    {
        return $this->container->make(RemoveUnvalidatedUsersController::class);
    }

    public function __construct(Application $app)
    {
        parent::__construct($app);
    }


}
