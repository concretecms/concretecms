<?php
namespace Concrete\Core\Summary;

use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Page\Page;
use Concrete\Core\Summary\Data\Extractor\Driver\BasicPageDriver;
use Concrete\Core\Summary\Data\Extractor\Driver\DriverManager;
use Concrete\Core\Summary\Data\Extractor\Driver\PageDriver;
use Concrete\Core\Summary\Data\Extractor\Driver\PageThumbnailDriver;
use Concrete\Core\Summary\Template\Renderer;

class ServiceProvider extends Provider
{
    public function register()
    {
        $this->app->singleton(DriverManager::class, function() {
            $driverManager = new DriverManager();
            $driverManager->register(BasicPageDriver::class);
            $driverManager->register(PageThumbnailDriver::class);
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
