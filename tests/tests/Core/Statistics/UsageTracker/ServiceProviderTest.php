<?php

namespace Concrete\tests\Core\Statistics\UsageTracker;

use Concrete\Core\Application\Application;
use Concrete\Core\Statistics\UsageTracker\AggregateTracker;
use Concrete\Core\Statistics\UsageTracker\ServiceProvider;
use Concrete\Core\Statistics\UsageTracker\TrackerManagerInterface;
use Concrete\Core\Support\Facade\Facade;

class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{

    public function testRegister()
    {
        $app = new Application();
        $app->instance('config', Facade::getFacadeApplication()->make('config'));

        $provider = new ServiceProvider($app);
        $provider->register();

        // Make sure the service provider provides everything we need
        $this->assertTrue($app->isShared(AggregateTracker::class));
        $this->assertTrue($app->bound(TrackerManagerInterface::class));
        $this->assertInstanceOf(TrackerManagerInterface::class, $app->make(AggregateTracker::class));
    }
}
