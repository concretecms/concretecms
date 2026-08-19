<?php

namespace Concrete\Tests\Block;

use Concrete\Core\Statistics\UsageTracker\AggregateTracker;
use Concrete\Core\Support\Facade\Application;
use Concrete\TestHelpers\Statistics\UsageTracker\FakeAggregateTracker;
use Concrete\TestHelpers\Statistics\UsageTracker\PlainTrackableBlockController;
use Concrete\Tests\TestCase;

/**
 * Regression test for #12531: BlockController must forward track()/forget() calls to
 * AggregateTracker for any TrackableInterface implementor, not just FileTrackableInterface
 * implementors. The stack proxy block controller is TrackableInterface-only, so without this
 * fix stack usage records are never created on save nor removed on delete.
 *
 * AggregateTracker is declared `final`, so a FakeAggregateTracker test double (implementing the
 * same TrackerManagerInterface) is bound into the container instead of a PHPUnit mock.
 */
class BlockControllerTrackableTest extends TestCase
{
    public function testSaveTracksPlainTrackableController(): void
    {
        $app = Application::getFacadeApplication();

        $tracker = new FakeAggregateTracker();
        $app->instance(AggregateTracker::class, $tracker);

        $controller = new PlainTrackableBlockController();
        $controller->setApplication($app);

        $controller->save([]);

        $this->assertCount(1, $tracker->tracked);
        $this->assertSame($controller, $tracker->tracked[0]);
        $this->assertCount(0, $tracker->forgotten);
    }

    public function testDeleteForgetsPlainTrackableController(): void
    {
        $app = Application::getFacadeApplication();

        $tracker = new FakeAggregateTracker();
        $app->instance(AggregateTracker::class, $tracker);

        $controller = new PlainTrackableBlockController();
        $controller->setApplication($app);

        $controller->delete();

        $this->assertCount(1, $tracker->forgotten);
        $this->assertSame($controller, $tracker->forgotten[0]);
        $this->assertCount(0, $tracker->tracked);
    }
}