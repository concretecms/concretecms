<?php

namespace Concrete\Core\Automation\Task;

use Concrete\Core\Automation\Task\Manager;
use Concrete\Core\Foundation\Service\Provider;

class ServiceProvider extends Provider
{

    public function register()
    {
        $this->app->singleton(Manager::class);
    }
}
