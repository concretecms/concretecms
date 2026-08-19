<?php

namespace Concrete\TestHelpers\Statistics\UsageTracker;

use Concrete\Core\Statistics\UsageTracker\TrackableInterface;
use Concrete\Core\Statistics\UsageTracker\TrackerManagerInterface;

/**
 * A hand-written test double for Concrete\Core\Statistics\UsageTracker\AggregateTracker.
 *
 * AggregateTracker is declared `final`, so PHPUnit's createMock()/createStub() cannot double it
 * directly (ClassIsFinalException). Since nothing in the production code type-hints a local
 * variable against the concrete AggregateTracker class - it's only ever resolved out of the
 * container via `$app->make(AggregateTracker::class)` and immediately called - substituting an
 * object that implements the same interface (TrackerManagerInterface) and is bound into the
 * container under that class name works exactly the same way at runtime.
 *
 * This double is bound as a shared container instance (`$app->instance(AggregateTracker::class,
 * ...)`), and the application container is shared across the whole PHPUnit process. Any test
 * that binds this double MUST unbind it in tearDown() (e.g. via
 * `$app->forgetInstance(AggregateTracker::class)`), otherwise it leaks into every later test that
 * saves or deletes a TrackableInterface block controller - which includes every
 * FileTrackableInterface block (Image, File, etc.), since FileTrackableInterface extends
 * TrackableInterface.
 */
class FakeAggregateTracker implements TrackerManagerInterface
{
    /**
     * @var TrackableInterface[]
     */
    public $tracked = [];

    /**
     * @var TrackableInterface[]
     */
    public $forgotten = [];

    /**
     * @var callable|null
     */
    public $onTrack;

    /**
     * {@inheritdoc}
     */
    public function track(TrackableInterface $trackable)
    {
        $this->tracked[] = $trackable;
        if ($this->onTrack !== null) {
            call_user_func($this->onTrack, $trackable);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function forget(TrackableInterface $trackable)
    {
        $this->forgotten[] = $trackable;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addTracker($tracker, callable $creator)
    {
        return $this;
    }
}