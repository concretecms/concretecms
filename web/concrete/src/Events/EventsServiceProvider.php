<?php
namespace Concrete\Core\Events;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;

class EventsServiceProvider extends ServiceProvider
{

    public function register()
    {
        if (!$this->app->bound('director')) {
            $this->app->bindShared('director', function ($app) {
                return new \Symfony\Component\EventDispatcher\EventDispatcher();
            });
        }
    }

}
