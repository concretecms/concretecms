<?php
namespace Concrete\Core\Statistics\UsageTracker;

use Concrete\Core\Application\ApplicationAwareInterface;
use InvalidArgumentException;
use Concrete\Core\Application\ApplicationAwareTrait;

/**
 * Class PolyTracker
 * A tracker that employes `\Illuminate\Support\Manager` to keep track of a list of Trackers.
 * When `::track` is called, `PolyTracker` forwards the call to each of its drivers.
 */
final class AggregateTracker implements TrackerManagerInterface, ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /** @var TrackerInterface[] */
    protected $trackers = [];

    /** @var callable[] */
    protected $creators = [];

    /** @var string[] */
    protected $map = [];

    /**
     * Track a trackable object
     * Any object could be passed to this method so long as it implements TrackableInterface
     * @param \Concrete\Core\Statistics\UsageTracker\TrackableInterface $trackable
     * @return static|TrackerInterface
     */
    public function track(TrackableInterface $trackable)
    {
        foreach ($this->getTrackerGenerator() as $tracker) {
            $tracker->track($trackable);
        }

        return $this;
    }

    /**
     * Forget a trackable object
     * Any object could be passed to this method so long as it implements TrackableInterface
     * @param \Concrete\Core\Statistics\UsageTracker\TrackableInterface $trackable
     * @return static|TrackerInterface
     */
    public function forget(TrackableInterface $trackable)
    {
        foreach ($this->getTrackerGenerator() as $tracker) {
            $tracker->forget($trackable);
        }

        return $this;
    }

    /**
     * Register a custom tracker creator Closure.
     *
     * @param  string $tracker The handle of the tracker
     * @param  callable $creator The closure responsible for returning the new tracker instance
     * @return static
     */
    public function addTracker($tracker, callable $creator)
    {
        $this->creators[$tracker] = $creator;
        $this->map[] = $tracker;

        if (isset($this->trackers[$tracker])) {
            unset($this->trackers[$tracker]);
        }

        return $this;
    }

    /**
     * Get a tracker by handle
     * @param $tracker
     * @return TrackerInterface
     */
    public function tracker($tracker)
    {
        // We've already made this tracker, so just return it.
        if ($cached = array_get($this->trackers, $tracker)) {
            return $cached;
        }

        // We've got a creator, lets create the tracker
        if ($creator = array_get($this->creators, $tracker)) {
            // Create through Container
            $created = $this->app->call($creator);

            $this->trackers[$tracker] = $created;
            unset($this->creators[$tracker]);

            return $created;
        }

        throw new InvalidArgumentException("Tracker [$tracker] not supported.");
    }

    /**
     * @return \Generator|TrackerInterface[]
     */
    private function getTrackerGenerator()
    {
        foreach ($this->map as $tracker) {
            yield $this->tracker($tracker);
        }
    }

}
