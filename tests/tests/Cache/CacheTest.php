<?php


namespace Concrete\Tests\Cache;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Cache\Level\ObjectCache;
use PHPUnit_Framework_TestCase;
use Stash\Driver\BlackHole;
use Stash\Driver\Composite;
use Stash\Driver\Ephemeral;
use Stash\Pool;

class CacheTest extends PHPUnit_Framework_TestCase
{

    protected $app;

    public function setUp()
    {
        $this->app = clone Application::getFacadeApplication();
        $this->app['config'] = clone $this->app['config'];
    }

    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testNoDrivers()
    {
        $app = Application::getFacadeApplication();
        $config = $app['config'];
        $config['concrete.cache.levels.object.drivers'] = [];
        $config['concrete.cache.levels.object.preferred_driver'] = [];
        $cacheLocal = $app->make('cache');
        $reflection = new \ReflectionClass(ObjectCache::class);
        $loadConfigMethod = $reflection->getMethod('loadConfig');
        $loadConfigMethod->setAccessible(true);
        $driver = $loadConfigMethod->invokeArgs($cacheLocal, ['object']);
        $this->assertInstanceOf(BlackHole::class, $driver);

    }
    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testPreferredDriver() {

        $app = Application::getFacadeApplication();
        $cacheLocal = $app->make('cache');
        $app = Application::getFacadeApplication();
        $reflection = new \ReflectionClass(ObjectCache::class);
        $loadConfigMethod = $reflection->getMethod('loadConfig');
        $loadConfigMethod->setAccessible(true);
        $app['config']['concrete.cache.levels.object.preferred_driver'] = ['core_ephemeral', 'core_filesystem'];
        $driver = $loadConfigMethod->invokeArgs($cacheLocal, ['object']);
        $this->assertInstanceOf(Composite::class, $driver);
        $app['config']['concrete.cache.levels.object.preferred_driver'] = 'core_ephemeral';
        $driver = $loadConfigMethod->invokeArgs($cacheLocal, ['object']);
        $this->assertInstanceOf(Ephemeral::class, $driver);
        $this->assertInstanceOf(Pool::class, $cacheLocal->pool);

    }



    /**
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     */
    public function testEnableDisableCache() {
        $app = Application::getFacadeApplication();
        $requestCache = $app->make('cache/request');
        $requestCache->disable();
        $this->assertFalse($requestCache->isEnabled());
        $expensiveCache = $app->make('cache/expensive');
        $expensiveCache->disable();
        $this->assertFalse($expensiveCache->isEnabled());
        $objectCache = $app->make('cache');
        $objectCache->disable();
        $this->assertFalse($objectCache->isEnabled());
        ObjectCache::enableAll();
        $this->assertTrue($requestCache->isEnabled());
        $this->assertTrue($expensiveCache->isEnabled());
        $this->assertTrue($objectCache->isEnabled());
        ObjectCache::disableAll();
        $this->assertFalse($requestCache->isEnabled());
        $this->assertFalse($expensiveCache->isEnabled());
        $this->assertFalse($objectCache->isEnabled());
        ObjectCache::enableAll();
    }

    public function tearDown()
    {
        Application::setFacadeApplication($this->app);
        $this->app = null;
        parent::tearDown();
    }
}