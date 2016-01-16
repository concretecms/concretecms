<?php

namespace Concrete\tests\Core\Statistics\UsageTracker;

use Concrete\Core\Application\Application;
use Concrete\Core\Statistics\UsageTracker\AggregateTracker;
use Concrete\Core\Statistics\UsageTracker\TrackableInterface;
use Concrete\Core\Statistics\UsageTracker\TrackerInterface;
use InvalidArgumentException;
use ReflectionClass;
use stdClass;

class AggregateTrackerTest extends \PHPUnit_Framework_TestCase
{

    /** @var Application */
    private $app;

    /** @var AggregateTracker */
    private $tracker;

    public function setUp()
    {
        $this->app = new Application();
        $this->tracker = $this->app->build(AggregateTracker::class);
    }

    public function tearDown()
    {
        $this->app = $this->tracker = null;
    }

    public function testCallsTrack()
    {
        // Create the trackers
        $tracker1 = $this->getMock(TrackerInterface::class);
        $tracker2 = $this->getMock(TrackerInterface::class);
        $tracker3 = $this->getMock(TrackerInterface::class);

        // Change the properties
        $this->changeProperty($this->tracker, 'trackers', [$tracker1, $tracker2, $tracker3]);
        $this->changeProperty($this->tracker, 'map', [0, 1, 2]);

        // Set the expectations
        $tracker1->expects($this->once())->method('track');
        $tracker2->expects($this->once())->method('track');
        $tracker3->expects($this->once())->method('track');

        // Run the track;
        $this->tracker->track($this->getMock(TrackableInterface::class));
    }

    public function testCallsForget()
    {
        // Create the trackers
        $tracker1 = $this->getMock(TrackerInterface::class);
        $tracker2 = $this->getMock(TrackerInterface::class);
        $tracker3 = $this->getMock(TrackerInterface::class);

        // Change the properties
        $this->changeProperty($this->tracker, 'trackers', [$tracker1, $tracker2, $tracker3]);
        $this->changeProperty($this->tracker, 'map', [0, 1, 2]);

        // Set the expectations
        $tracker1->expects($this->once())->method('forget');
        $tracker2->expects($this->once())->method('forget');
        $tracker3->expects($this->once())->method('forget');

        // Run the track;
        $this->tracker->forget($this->getMock(TrackableInterface::class));
    }

    public function testCallsCreator()
    {
        $return_tracker = $this->getMock(TrackerInterface::class);
        $this->tracker->addTracker('test', function() use ($return_tracker) {
            return $return_tracker;
        });

        // Expect that the `track` method gets called
        $return_tracker->expects($this->once())->method('track');

        // Run the track method
        $this->tracker->track($this->getMock(TrackableInterface::class));
    }

    public function testUsesDIContainer()
    {
        $this->app->bind(stdClass::class, function() {
            return (object)['test' => 'tested'];
        });

        $passed = null;
        $return_tracker = $this->getMock(TrackerInterface::class);
        $this->tracker->addTracker('test', function(stdClass $obj) use (&$passed, $return_tracker) {
            $passed = $obj;
            return $return_tracker;
        });

        // Run the track method
        $this->tracker->track($this->getMock(TrackableInterface::class));

        // Make sure it gave us the right stdclass
        $this->assertInstanceOf(stdClass::class, $passed);
        $this->assertEquals('tested', $passed->test);
    }

    public function testSomeCreated()
    {
        // Create the trackers
        $tracker1 = $this->getMock(TrackerInterface::class);
        $tracker2 = $this->getMock(TrackerInterface::class);
        $tracker3 = $this->getMock(TrackerInterface::class);
        $tracker4 = $this->getMock(TrackerInterface::class);

        $calls = 0;
        // Bind the trackers
        $this->tracker
            ->addTracker('test1', function() use ($tracker1, &$calls) { $calls++; return $tracker1; })
            ->addTracker('test2', function() use ($tracker2, &$calls) { $calls++; return $tracker2; })
            ->addTracker('test3', function() use ($tracker3, &$calls) { $calls++; return $tracker3; })
            ->addTracker('test4', function() use ($tracker4, &$calls) { $calls++; return $tracker4; });

        // Set the expectations
        $tracker1->expects($this->exactly(2))->method('track');
        $tracker2->expects($this->exactly(2))->method('track');
        $tracker3->expects($this->exactly(2))->method('track');
        $tracker4->expects($this->exactly(2))->method('track');

        // Create a couple of them
        $this->tracker->tracker('test1');
        $this->tracker->tracker('test3');

        // Make sure we only have 2 calls so far
        $this->assertEquals(2, $calls);

        // Now track something
        $this->tracker->track($this->getMock(TrackableInterface::class));

        // Make sure there were only 4 calls
        $this->assertEquals(4, $calls);

        // Now track one more thing
        $this->tracker->track($this->getMock(TrackableInterface::class));

        // Make sure there were only 4 calls
        $this->assertEquals(4, $calls);
    }

    public function testReturnsInstance()
    {
        $result = $this->tracker->track($this->getMock(TrackableInterface::class));
        $this->assertSame($this->tracker, $result);
    }

    public function testOverwrite()
    {
        $tracker1 = $this->getMock(TrackerInterface::class);
        $tracker2 = $this->getMock(TrackerInterface::class);

        // Bind 'test' to the first tracker
        $this->tracker->addTracker('test', function () use ($tracker1) { return $tracker1; });

        // Lets make sure it's really the right tracker twice to test retrieving from cache
        $this->assertEquals($tracker1, $this->tracker->tracker('test'));
        $this->assertEquals($tracker1, $this->tracker->tracker('test'));

        // Rebind 'test' to the other tracker
        $this->tracker->addTracker('test', function () use ($tracker2) { return $tracker2; });

        // Make sure it's the right tracker again
        $this->assertEquals($tracker2, $this->tracker->tracker('test'));
        $this->assertEquals($tracker2, $this->tracker->tracker('test'));
    }

    public function testErrorOnMiss()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        $this->tracker->tracker('test');
    }

    private function changeProperty($object, $property, $value)
    {
        $reflection = new ReflectionClass($object);
        $property = $reflection->getProperty($property);
        $property->setAccessible(true);

        $property->setValue($object, $value);
    }

}
