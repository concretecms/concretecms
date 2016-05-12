<?php

namespace Concrete\Core\Statistics\UsageTracker;

use Concrete\Core\Application\Application;
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
            /** @var AggregateTracker $tracker */
            $tracker = $app->build(AggregateTracker::class);

            if ($trackers = $app['config']['statistics.trackers']) {
                foreach ($trackers as $key => $tracker_string) {
                    $tracker->addTracker($key, function (Application $app) use ($tracker_string) {
                        return $app->make($tracker_string);
                    });
                }
            }

            return $tracker;
        });

        // Bind the manager interface to the tracker singleton
        $this->app->bind(TrackerManagerInterface::class, AggregateTracker::class);

        // Bind a useful abstract string
        $this->app->bind('statistics/tracker', AggregateTracker::class);
    }

}
