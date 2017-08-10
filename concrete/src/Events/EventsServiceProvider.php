<?php

namespace Concrete\Core\Events;

use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventsServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Set the singleton on the dispatcher implementation
        $this->app->singleton(EventDispatcher::class);

        // Bind the interface to the implementation
        $this->app->bindIf(EventDispatcherInterface::class, EventDispatcher::class);

        // Add the 'director' alias in a backwards compatible way.
        $this->app->bindIf('director', EventDispatcherInterface::class);
    }
}
