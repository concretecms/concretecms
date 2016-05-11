<?php

namespace Concrete\Core\Statistics\UsageTracker;

interface TrackerInterface
{

    /**
     * Track a trackable object
     * Any object could be passed to this method so long as it implements TrackableInterface
     * @param \Concrete\Core\Statistics\UsageTracker\TrackableInterface $trackable
     * @return static|TrackerInterface
     */
    public function track(TrackableInterface $trackable);

    /**
     * Forget a trackable object
     * Any object could be passed to this method so long as it implements TrackableInterface
     * @param \Concrete\Core\Statistics\UsageTracker\TrackableInterface $trackable
     * @return static|TrackerInterface
     */
    public function forget(TrackableInterface $trackable);

}
