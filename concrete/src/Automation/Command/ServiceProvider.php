<?php

namespace Concrete\Core\Automation\Command;

use Concrete\Core\Automation\Command\Manager;
use Concrete\Core\Foundation\Service\Provider;

class ServiceProvider extends Provider
{

    public function register()
    {
        $this->app->singleton(Manager::class);
    }
}
