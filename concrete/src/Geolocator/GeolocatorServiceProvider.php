<?php
namespace Concrete\Core\Geolocator;

use Concrete\Core\Application\Application;
use Concrete\Core\Entity\Geolocator;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use IPLib\Address\AddressInterface;

class GeolocatorServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(GeolocatorService::class);
        $this->app->bind(Geolocator::class, function (Application $app) {
            return $app->make(GeolocatorService::class)->getCurrent();
        });
        $this->app->bind(GeolocationResult::class, function (Application $app, array $parameters = []) {
            $geolocator = $app->make(Geolocator::class);
            if ($geolocator == null) {
                $result = new GeolocationResult();
                $result->setError(GeolocationResult::ERR_NOCURRENTLIBRARY);
            } else {
                if (array_key_exists('ip', $parameters)) {
                    $ip = $parameters['ip'];
                } else {
                    $ip = $app->make('ip')->getRequestIPAddress();
                }
                if ($ip instanceof AddressInterface) {
                    $geolocatorController = $app->make(GeolocatorService::class)->getController($geolocator);
                    $result = $geolocatorController->geolocateIPAddress($ip);
                } else {
                    $result = new GeolocationResult();
                    $result->setError(GeolocationResult::ERR_NOCURRENTIPADDRESS);
                }
            }

            return $result;
        });
    }
}
