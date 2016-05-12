<?php

namespace Concrete\tests\Core\Statistics\UsageTracker;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\LoaderInterface;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Config\SaverInterface;
use Concrete\Core\Statistics\UsageTracker\AggregateTracker;
use Concrete\Core\Statistics\UsageTracker\ServiceProvider;
use Concrete\Core\Statistics\UsageTracker\TrackableInterface;
use Concrete\Core\Statistics\UsageTracker\TrackerInterface;
use Concrete\Core\Statistics\UsageTracker\TrackerManagerInterface;

class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{

    public function testRegister()
    {
        $app = $this->createApplication();

        $provider = new ServiceProvider($app);
        $provider->register();

        // Make sure the service provider provides everything we need
        $this->assertTrue($app->isShared(AggregateTracker::class));
        $this->assertTrue($app->bound(TrackerManagerInterface::class));
        $this->assertInstanceOf(TrackerManagerInterface::class, $app->make(AggregateTracker::class));
    }

    public function testCreatingTrackersFromConfig()
    {
        $app = $this->createApplication();
        $repository = $app['config'];

        // A tracker that will be called
        $tracker1 = $this->getMock(TrackerInterface::class);
        $tracker1->expects($this->once())->method('track');

        // A tracker that will not be called
        $tracker2 = $this->getMock(TrackerInterface::class);
        $tracker2->expects($this->never())->method('track');

        // And another tracker that will be called
        $tracker3 = $this->getMock(TrackerInterface::class);
        $tracker3->expects($this->once())->method('track');

        // Bind three so that we can register two of them and be sure that the third one doesn't effect the default tracker
        $app->bind('tracker/1', $this->returnCallable($tracker1));
        $app->bind('tracker/2', $this->returnCallable($tracker2));
        $app->bind('tracker/3', $this->returnCallable($tracker3));

        // Lets set the config item
        $repository->set('statistics.trackers', [
            'foo' => 'tracker/1',
            'bar' => 'tracker/3'
        ]);

        // Register the service provider
        $provider = new ServiceProvider($app);
        $provider->register();

        /** @var TrackerManagerInterface $tracker */
        $tracker = $app->make(TrackerManagerInterface::class);

        // If all is well, this method should call track on tracker1 and tracker3 but not on tracker2
        $tracker->track($this->getMock(TrackableInterface::class));
    }

    /**
     * Method to make it easy to bind an existing object to the IoC
     *
     * @param $tracker
     * @return \Closure
     */
    private function returnCallable($tracker)
    {
        return function () use ($tracker) {
            return $tracker;
        };
    }

    /**
     * Create an application object to test with
     * @return \Concrete\Core\Application\Application
     */
    private function createApplication()
    {
        $app = new Application();

        // This service provider requires a config repository be registered
        $loader = $this->getMock(LoaderInterface::class);
        $saver = $this->getMock(SaverInterface::class);
        $repository = new Repository($loader, $saver, 'test');

        $app->bind('config', $this->returnCallable($repository));
        $app->bind(Application::class, $this->returnCallable($app));

        return $app;
    }

}
