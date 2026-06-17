<?php

namespace Concrete\Core\Statistics\UsageTracker;

interface TrackerInterface
{
    /**
     * Track a trackable object.
     *
     * @param \Concrete\Core\Statistics\UsageTracker\TrackableInterface $trackable
     *
     * @return $this
     */
    public function track(TrackableInterface $trackable);

    /**
     * Forget a trackable object.
     *
     * @param \Concrete\Core\Statistics\UsageTracker\TrackableInterface $trackable
     *
     * @return $this
     */
    public function forget(TrackableInterface $trackable);

}
