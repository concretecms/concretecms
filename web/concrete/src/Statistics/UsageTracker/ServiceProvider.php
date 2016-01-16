<?php

namespace Concrete\Core\Statistics\UsageTracker;

use Concrete\Core\Foundation\Service\Provider;

class ServiceProvider extends Provider
{

    /**
     * Registers the services provided by this provider.
     */
    public function register()
    {
        // Make the main tracker manager a singleton
        $this->app->singleton(AggregateTracker::class, function($app) {
            $tracker = $app->build(AggregateTracker::class);

            return $tracker;
        });

        // Bind the manager interface to the tracker singleton
        $this->app->bind(TrackerManagerInterface::class, PolyTracker::class);
    }

}
