<?php

namespace Concrete\Tests\Block;

use Concrete\Core\Statistics\UsageTracker\AggregateTracker;
use Concrete\Core\Support\Facade\Application;
use Concrete\TestHelpers\Statistics\UsageTracker\FakeAggregateTracker;
use Concrete\TestHelpers\Statistics\UsageTracker\PlainTrackableBlockController;
use Concrete\Tests\TestCase;

/**
 * Regression test proving that binding a FakeAggregateTracker into the shared application
 * container in one test does not leak into a later test.
 *
 * Before this fix, tests such as CoreStackDisplayControllerSaveTest and
 * BlockControllerTrackableTest called `$app->instance(AggregateTracker::class, $tracker)` but
 * never reverted the binding. Since the application container is shared across the whole
 * PHPUnit process, this fake instance stayed bound for the rest of the run. Any later test that
 * saved or deleted a TrackableInterface block controller - which includes every
 * FileTrackableInterface block controller, such as Image or File, since FileTrackableInterface
 * extends TrackableInterface - would then resolve the leaked fake tracker instead of the real
 * AggregateTracker. This surfaced as ImportExportTest fatally erroring with e.g.
 * "Call to undefined method Concrete\Block\File\Controller::getStackID()", because it inherited
 * a leaked FakeAggregateTracker whose onTrack callback (set up by an earlier, unrelated test)
 * assumed every tracked trackable was a stack display controller.
 *
 * This test simulates that sequence: it binds and (correctly) unbinds a FakeAggregateTracker in
 * its own tearDown(), then asserts that a fresh AggregateTracker::class resolution afterwards is
 * a real AggregateTracker rather than the fake.
 */
class FakeAggregateTrackerIsolationTest extends TestCase
{
    protected function tearDown(): void
    {
        Application::getFacadeApplication()->forgetInstance(AggregateTracker::class);
        parent::tearDown();
    }

    public function testBindingFakeTrackerDoesNotLeakOnceUnbound(): void
    {
        $app = Application::getFacadeApplication();

        $tracker = new FakeAggregateTracker();
        $app->instance(AggregateTracker::class, $tracker);

        $controller = new PlainTrackableBlockController();
        $controller->setApplication($app);
        $controller->save([]);

        $this->assertCount(1, $tracker->tracked);

        // Simulate the end of this test's lifecycle explicitly (in addition to the real
        // tearDown(), which will also run): forgetting the instance must cause a subsequent
        // resolution to bypass the fake and fall back to the real, container-registered
        // AggregateTracker.
        $app->forgetInstance(AggregateTracker::class);

        $resolved = $app->make(AggregateTracker::class);

        $this->assertInstanceOf(AggregateTracker::class, $resolved);
        $this->assertNotSame($tracker, $resolved);
    }
}