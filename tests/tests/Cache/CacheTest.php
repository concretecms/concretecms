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
    public function testPreferredDriver() {

        $cacheLocal = $this->app->make('cache');
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
    public function testNoDrivers()
    {
        $app = Application::getFacadeApplication();
        $config = $app['config'];
        $config['concrete.cache.levels.object.drivers'] = [];
        $config['concrete.cache.levels.object.preferred_driver'] = [];
        $cacheLocal = $this->app->make('cache');
        $reflection = new \ReflectionClass(ObjectCache::class);
        $loadConfigMethod = $reflection->getMethod('loadConfig');
        $loadConfigMethod->setAccessible(true);
        $driver = $loadConfigMethod->invokeArgs($cacheLocal, ['object']);
        $this->assertInstanceOf(BlackHole::class, $driver);

    }

    public function tearDown()
    {
        Application::setFacadeApplication($this->app);
        $config = $this->app['config'];
        $this->app = null;
        parent::tearDown();
    }
}