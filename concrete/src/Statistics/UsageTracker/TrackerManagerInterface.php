<?php

namespace Concrete\Core\Statistics\UsageTracker;

/**
 * @since 8.0.0
 */
interface TrackerManagerInterface extends TrackerInterface
{

    /**
     * Register a custom tracker creator callable.
     *
     * @param  string $tracker The handle of the tracker
     * @param  callable $creator The callable responsible for returning the new tracker instance
     * @return static
     */
    public function addTracker($tracker, callable $creator);

}
