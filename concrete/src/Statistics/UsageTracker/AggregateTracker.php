<?php
namespace Concrete\Core\Statistics\UsageTracker;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use InvalidArgumentException;

/**
 * A tracker that wraps a list of trackers.
 * When `::track()` is called, this class forwards the call to each of its trackers.
 */
final class AggregateTracker implements TrackerManagerInterface, ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * A list of all the registered tracker handles.
     *
     * @var string[]
     */
    private $map = [];

    /**
     * The registered tracker creators, mapped by their handle.
     * Once a creator is invoked, it's removed from this map.
     *
     * @var array|callable[]
     */
    private $creators = [];

    /**
     * The trackers created by the tracker creators, mapped by their handle.
     *
     * @var \Concrete\Core\Statistics\UsageTracker\TrackerInterface[]
     */
    private $trackers = [];

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Statistics\UsageTracker\TrackerInterface::track()
     */
    public function track(TrackableInterface $trackable)
    {
        foreach ($this->getTrackerGenerator() as $tracker) {
            $tracker->track($trackable);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Statistics\UsageTracker\TrackerInterface::forget()
     */
    public function forget(TrackableInterface $trackable)
    {
        foreach ($this->getTrackerGenerator() as $tracker) {
            $tracker->forget($trackable);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Statistics\UsageTracker\TrackerManagerInterface::addTracker()
     */
    public function addTracker($tracker, callable $creator)
    {
        $this->creators[$tracker] = $creator;
        if (($i = array_search($tracker, $this->map, true)) !== false) {
            array_splice($this->map, $i, 1);
        }
        $this->map[] = $tracker;
        unset($this->trackers[$tracker]);

        return $this;
    }

    /**
     * Get a tracker by handle.
     *
     * @param string $tracker
     *
     * @throws \InvalidArgumentException if $tracker an unknown tracker handle
     *
     * @return \Concrete\Core\Statistics\UsageTracker\TrackerInterface
     */
    public function tracker($tracker)
    {
        if ($cached = $this->trackers[$tracker] ?? null) {
            return $cached;
        }

        if ($creator = $this->creators[$tracker] ?? null) {
            $created = $this->app->call($creator);
            $this->trackers[$tracker] = $created;
            unset($this->creators[$tracker]);

            return $created;
        }

        throw new InvalidArgumentException("Tracker [$tracker] not supported.");
    }

    /**
     * Generate a list of all the registered trackers.
     *
     * @return \Generator|\Concrete\Core\Statistics\UsageTracker\TrackerInterface[]
     */
    private function getTrackerGenerator()
    {
        foreach ($this->map as $tracker) {
            yield $this->tracker($tracker);
        }
    }
}
