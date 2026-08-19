<?php

namespace Concrete\Tests\Block;

use Concrete\Block\CoreStackDisplay\Controller;
use Concrete\Core\Statistics\UsageTracker\AggregateTracker;
use Concrete\Core\Support\Facade\Application;
use Concrete\TestHelpers\Statistics\UsageTracker\FakeAggregateTracker;
use Concrete\Tests\TestCase;
use ReflectionProperty;

/**
 * Regression test for #12531: adding a stack to a page through the UI threw
 * Doctrine\ORM\Exception\MissingIdentifierField because AggregateTracker::track() (invoked by
 * BlockController::performSave(), which the earlier #12531 fix made reachable for
 * TrackableInterface implementors such as this stack proxy controller) ran before
 * Controller::save() had assigned $this->stID from $args. UsageTracker::trackBlocks() reads
 * getStackID() to build a StackUsageRecord whose stack_id is part of its composite primary key,
 * so a null value there caused EntityManager::merge() to throw.
 *
 * AggregateTracker is declared `final`, so a FakeAggregateTracker test double (implementing the
 * same TrackerManagerInterface) is bound into the container instead of a PHPUnit mock.
 *
 * This test isolates the ordering bug from Controller::save()'s DB persistence branch
 * (performSave() calling Database::connection()->MetaColumnNames($this->btTable), which
 * performs real schema introspection). Since that branch isn't what's under test here and this
 * lightweight test doesn't set up the block's database table, $btTable is temporarily cleared
 * via reflection so performSave() skips it and only the tracking-order behaviour is exercised.
 *
 * The fake tracker is bound into the shared application container via `$app->instance()`. Since
 * that container instance is shared across the whole PHPUnit process, the binding MUST be
 * reverted in tearDown() - otherwise every subsequent test that saves any TrackableInterface
 * block (which includes every FileTrackableInterface block, e.g. Image, File) resolves this
 * fake tracker instead of the real AggregateTracker, and this test's onTrack callback (which
 * assumes the trackable is always a stack display Controller) gets invoked with unrelated block
 * controllers, throwing "Call to undefined method ...Controller::getStackID()".
 */
class CoreStackDisplayControllerSaveTest extends TestCase
{
    protected function tearDown(): void
    {
        Application::getFacadeApplication()->forgetInstance(AggregateTracker::class);
        parent::tearDown();
    }

    public function testStackIdIsAssignedBeforeTrackingRuns(): void
    {
        $app = Application::getFacadeApplication();

        $tracker = new FakeAggregateTracker();
        $observedStackIdDuringTrack = 'not-called';
        $tracker->onTrack = function ($trackable) use (&$observedStackIdDuringTrack) {
            /** @var Controller $trackable */
            $observedStackIdDuringTrack = $trackable->getStackID();
        };

        $app->instance(AggregateTracker::class, $tracker);

        $controller = new Controller();
        $controller->setApplication($app);

        // Skip performSave()'s DB persistence branch; it's not what this test is about, and this
        // lightweight test doesn't set up btCoreStackDisplay's schema.
        $btTable = new ReflectionProperty(Controller::class, 'btTable');
        $btTable->setAccessible(true);
        $btTable->setValue($controller, null);

        $controller->save(['stID' => 42]);

        $this->assertSame(
            42,
            $observedStackIdDuringTrack,
            'getStackID() must already return the new stack ID while AggregateTracker::track() runs during save(), otherwise UsageTracker::persist() attempts to store a StackUsageRecord with a null stack_id (part of its composite primary key), which throws Doctrine\ORM\Exception\MissingIdentifierField.'
        );

        $this->assertSame(42, $controller->getStackID());
        $this->assertCount(1, $tracker->tracked);
    }
}