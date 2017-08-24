<?php
namespace Concrete\Core\Geolocator;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Geolocator;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class GeolocatorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(GeolocatorService::class);
        $this->app->bind(Geolocator::class, function (Application $app) {
            return $app->make(GeolocatorService::class)->getCurrent();
        });
    }
}
