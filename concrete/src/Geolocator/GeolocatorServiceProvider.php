<?php
namespace Concrete\Core\Geolocator;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Geolocator;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Concrete\Core\Utility\IPAddress;

class GeolocatorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(GeolocatorService::class);
        $this->app->bind(Geolocator::class, function (Application $app) {
            return $app->make(GeolocatorService::class)->getCurrent();
        });
        $this->app->bind(GeolocationResult::class, function (Application $app, IPAddress $ip = null) {
            $geolocator = $app->make(Geolocator::class);
            if ($geolocator == null) {
                $result = null;
            } else {
                if ($ip === null) {
                    $ip = $app->make('ip')->getRequestIPAddress();
                }
                $geolocatorController = $app->make(GeolocatorService::class)->getController($geolocator);
                $result = $geolocatorController->geolocateIPAddress($ip);
            }

            return $result;
        });
    }
}
