<?php
namespace Concrete\Core\Board;

use Concrete\Core\Board\DataSource\Driver\Manager;
use Concrete\Core\Board\Instance\Slot\CollectionFactory;
use Concrete\Core\Foundation\Service\Provider;

class ServiceProvider extends Provider
{
    public function register()
    {
        $this->app->singleton(Manager::class);
        $this->app->singleton(CollectionFactory::class);
    }

}
