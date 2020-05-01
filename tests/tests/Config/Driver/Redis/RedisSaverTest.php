<?php

namespace Concrete\Tests\Config\Driver\Redis;

use Concrete\Core\Config\Driver\Redis\RedisSaver;
use Mockery as M;

/**
 * @depends Concrete\Tests\Config\Driver\Redis\RedisPaginatedTraitTest::testPaginatedScan
 */
class RedisSaverTest extends \PHPUnit_Framework_TestCase
{

    use M\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /**
     * @dataProvider namespacesToTest
     */
    public function testSave($namespace)
    {
        $expectedIterator = null;
        $expectedSearch = 'cfg=' . $namespace . '::test.foo.*';
        $expectedBatch = 100;

        // Bind expectations
        $redis = M::mock(\Redis::class);
        $redis->shouldReceive('scan')->once()->with($expectedIterator, $expectedSearch, $expectedBatch)->andReturnUsing(function(&$iterator, $search, $batch) use ($namespace) {
            if (!$iterator) {
                $iterator = 1;
                return ['cfg=' . $namespace . '::test.foo.test'];
            }

            return false;
        });

        // Make sure we try to paginate properly
        $redis->shouldReceive('scan')->once()->with(1, $expectedSearch, $expectedBatch)->andReturnNull();
        // Make sure we try to delete existing keys
        $redis->shouldReceive('del')->with([$namespace . '::test.foo', $namespace . '::test.foo.test'])->andReturn(2);
        // Make sure we try to set the values as serialized
        $redis->shouldReceive('mset')->with([
            $namespace . '::test.foo.test' => serialize(10),
            $namespace . '::test.foo.subarray.subtest' => serialize(true),
            $namespace . '::test.foo.baz' => serialize('boo'),
        ])->andReturn(true);

        // Run the method we're testing
        $saver = new RedisSaver($redis);
        $saver->save('foo', ['test' => 10, 'subarray' => ['subtest' => true], 'baz' => 'boo'], 'testing', 'test', $namespace);
    }

    public function namespacesToTest()
    {
        return [
            [''],
            ['core'],
            ['test'],
        ];
    }

}
