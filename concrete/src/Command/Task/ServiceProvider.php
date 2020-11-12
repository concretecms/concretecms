<?php

namespace Concrete\Core\Command\Task;

use Concrete\Core\Command\Task\Manager;
use Concrete\Core\Foundation\Service\Provider;

class ServiceProvider extends Provider
{

    public function register()
    {
        $this->app->singleton(Manager::class);
    }
}
