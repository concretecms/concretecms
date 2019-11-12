<?php
namespace Concrete\Core\Summary;

use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Summary\Data\Extractor\Driver\DriverManager;
use Concrete\Core\Summary\Data\Extractor\Driver\PageDriver;

class ServiceProvider extends Provider
{
    public function register()
    {
        $this->app->singleton(DriverManager::class, function() {
            $driverManager = new DriverManager();
            $driverManager->register(PageDriver::class);
            return $driverManager;
        });
    }

}
