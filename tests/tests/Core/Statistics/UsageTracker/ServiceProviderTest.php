<?php

namespace Concrete\Tests\Core\Statistics\UsageTracker;

use Concrete\Core\Application\Application;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Statistics\UsageTracker\AggregateTracker;
use Concrete\Core\Statistics\UsageTracker\ServiceProvider;
use Concrete\Core\Statistics\UsageTracker\TrackerManagerInterface;

class ServiceProviderTest extends \PHPUnit_Framework_TestCase
{

    public function testRegister()
    {
        $app = new Application();

        // This service provider requires a config repository be registered
        $mockBuilder = $this->getMockBuilder(Repository::class);
        $repository = $mockBuilder->disableOriginalConstructor()->getMock();

        $app->bind('config', function() use ($repository) {
            return $repository;
        });

        $provider = new ServiceProvider($app);
        $provider->register();

        // Make sure the service provider provides everything we need
        $this->assertTrue($app->isShared(AggregateTracker::class));
        $this->assertTrue($app->bound(TrackerManagerInterface::class));
        $this->assertInstanceOf(TrackerManagerInterface::class, $app->make(AggregateTracker::class));
    }
}
