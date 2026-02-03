<?php

namespace Concrete\Core\Statistics\UsageTracker;

interface TrackerManagerInterface extends TrackerInterface
{
    /**
     * Register a custom tracker creator.
     *
     * @param string $tracker The handle of the tracker
     * @param callable $creator The closure responsible for returning the new tracker instance
     *
     * @return $this
     */
    public function addTracker($tracker, callable $creator);
}
