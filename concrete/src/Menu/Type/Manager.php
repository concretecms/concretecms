<?php

namespace Concrete\Core\Menu\Type;

use Concrete\Core\Application\Application;
use Concrete\Core\Support\Manager as CoreManager;

class Manager extends CoreManager
{

    public function __construct(Application $application)
    {
        $this->app = $application;
    }

    public function createDashboardDriver()
    {
        return $this->app->make(DashboardType::class);
    }

}
