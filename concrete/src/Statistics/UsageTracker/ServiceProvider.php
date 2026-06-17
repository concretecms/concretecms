<?php

namespace Concrete\Core\Statistics\UsageTracker;

use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider;

class ServiceProvider extends Provider
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\Service\Provider::register()
     */
    public function register()
    {
        $this->app->singleton(
            AggregateTracker::class,
            static function(Application $app) {
                $tracker = $app->build(AggregateTracker::class);
                if ($trackers = $app['config']['statistics.trackers']) {
                    foreach ($trackers as $key => $generatorAbstract) {
                        $tracker->addTracker(
                            $key,
                            static function (Application $app) use ($generatorAbstract) {
                                return $app->make($generatorAbstract);
                            }
                        );
                    }
                }

                return $tracker;
            }
        );
        $this->app->bind(TrackerManagerInterface::class, AggregateTracker::class);
        $this->app->bind('statistics/tracker', AggregateTracker::class);
    }
}
