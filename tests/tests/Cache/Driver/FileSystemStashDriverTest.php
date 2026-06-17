<?php

namespace Concrete\Tests\Cache\Driver;

use Concrete\Core\Support\Facade\Application;
use Concrete\TestHelpers\Database\ConcreteDatabaseTestCase;
use Stash\Interfaces\DriverInterface;
use Stash\Driver\FileSystem\EncoderInterface;
use Stash\Driver\FileSystem\NativeEncoder;
use Mockery;

/**
 * @runTestsInSeparateProcesses
 * @preserveGlobalState disabled
 */
class FileSystemStashDriverTest extends ConcreteDatabaseTestCase
{
    protected $tables = ['Logs'];

    public function setUp(): void
    {
        parent::setUp();

        $mock = Mockery::mock('overload:' . NativeEncoder::class, EncoderInterface::class);
        $mock->shouldReceive('getExtension', 'serialize');
        $mock->shouldReceive('deserialize')->andReturnUsing(function($path) {
            // This emulates a situation where the cache file is being included
            // after it was removed from the file system. This can happen if the
            // file was removed after the `file_exists` check in this class.
            include(__DIR__ . '/foobar.php');
        });
    }

    public function tearDown(): void
    {
        Mockery::close();

        parent::tearDown();
    }

    public function testExpensiveFilesystemCache(): void
    {
        $driver = $this->buildDriver('expensive');
        $this->storeTestData($driver, 'Testing data');

        $this->assertNull($driver->getData(['foobar']));
    }

    public function testOverridesFilesystemCache(): void
    {
        $driver = $this->buildDriver('overrides');
        $this->storeTestData($driver, 'Testing data');

        $this->assertNull($driver->getData(['foobar']));
    }

    private function storeTestData(DriverInterface $driver, string $data): void
    {
        $driver->storeData(['foobar'], $data, time() + 30);
    }

    private function buildDriver(string $level): DriverInterface
    {
        $app = Application::getFacadeApplication();
        $config = $app['config']->get("concrete.cache.levels.{$level}.drivers.core_filesystem");

        return new $config['class'](array_get($config, 'options'));
    }
}
