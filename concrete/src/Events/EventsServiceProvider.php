<?php

namespace Concrete\Core\Events;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventsServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Set the singleton on the dispatcher implementation
        $this->app->singleton(EventDispatcher::class);

        // Add the 'director' alias in a backwards compatible way.
        $this->app->alias(EventDispatcher::class, 'director');

        $this->app->singleton(EventDispatcherInterface::class, function() {
            return $this->app->make(EventDispatcher::class)->getEventDispatcher();
        });
    }
}
