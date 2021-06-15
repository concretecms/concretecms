<?php

namespace Concrete\Core\Events;

use Bernard\BernardEvents;
use Concrete\Core\Foundation\Service\Provider as ServiceProvider;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class EventsServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Set the singleton on the dispatcher implementation
        $this->app->singleton(EventDispatcher::class);

        // Bind the interface to the implementation
        $this->app->bindIf(EventDispatcherInterface::class, EventDispatcher::class, true);

        // Add the 'director' alias in a backwards compatible way.
        $this->app->alias(EventDispatcherInterface::class, 'director');
    }
}
