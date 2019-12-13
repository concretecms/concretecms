<?php
namespace Concrete\Core\Summary;

use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Page\Page;
use Concrete\Core\Summary\Data\Extractor\Driver\BasicCalendarEventDriver;
use Concrete\Core\Summary\Data\Extractor\Driver\BasicPageDriver;
use Concrete\Core\Summary\Data\Extractor\Driver\DriverManager;
use Concrete\Core\Summary\Template\Renderer;

class ServiceProvider extends Provider
{
    public function register()
    {
        $app = $this->app;
        $this->app->singleton(DriverManager::class, function() use ($app) {
            $driverManager = new DriverManager($app);
            $driverManager->register(BasicPageDriver::class);
            $driverManager->register(BasicCalendarEventDriver::class);
            return $driverManager;
        });

        $this->app
            ->when(Renderer::class)
            ->needs(Page::class)
            ->give(function () {
                return Page::getCurrentPage();
            });


    }

}
