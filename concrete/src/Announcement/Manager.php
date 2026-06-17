<?php
namespace Concrete\Core\Announcement;

use Concrete\Core\Announcement\Controller\CollectSiteInformationController;
use Concrete\Core\Announcement\Controller\Update\Version920Controller;
use Concrete\Core\Announcement\Controller\Update\Version929Controller;
use Concrete\Core\Announcement\Controller\Update\Version930Controller;
use Concrete\Core\Application\Application;
use Concrete\Core\Announcement\Controller\WelcomeController;
use Concrete\Core\Support\Manager as CoreManager;

defined('C5_EXECUTE') or die("Access Denied.");

class Manager extends CoreManager
{
    public function createWelcomeDriver()
    {
        return $this->app->make(WelcomeController::class);
    }

    public function createCollectSiteInformationDriver()
    {
        return $this->app->make(CollectSiteInformationController::class);
    }

    public function createConcreteVersion920Driver()
    {
        return $this->app->make(Version920Controller::class);
    }

    public function createConcreteVersion929Driver()
    {
        return $this->app->make(Version929Controller::class);
    }

    public function createConcreteVersion930Driver()
    {
        return $this->app->make(Version930Controller::class);
    }

    public function __construct(Application $application)
    {
        parent::__construct($application);
    }
}
