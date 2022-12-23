<?php
namespace Concrete\Core\Application\UserInterface\Welcome\Type;

use Concrete\Core\Application\Application;
use Concrete\Core\Support\Manager as CoreManager;

defined('C5_EXECUTE') or die("Access Denied.");

class Manager extends CoreManager
{
    public function createIntroductionDriver()
    {
        return $this->app->make(IntroductionType::class);
    }

    public function createSiteInformationDriver()
    {
        return $this->app->make(SiteInformationType::class);
    }

    public function __construct(Application $application)
    {
        parent::__construct($application);
    }
}
