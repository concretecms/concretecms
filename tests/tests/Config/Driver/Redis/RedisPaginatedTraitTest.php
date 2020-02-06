<?php

namespace Concrete\Tests\Config\Driver\Redis;

use Concrete\Core\Config\Driver\Redis\RedisPaginatedTrait;
use Concrete\Tests\Config\Driver\Redis\Fixtures\RedisPaginatedTraitFixture;
use Concrete\Tests\Config\Driver\Redis\Fixtures\RedisPaginatedTraitValuesFixture;
use Illuminate\Support\Arr;
use Mockery as M;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Redis;

class RedisPaginatedTraitTest extends \PHPUnit_Framework_TestCase
{

    use MockeryPHPUnitIntegration;

    public function testPaginatedScan()
    {
        $redis = M::mock(Redis::class);

        $redis->shouldReceive('scan')->once()->with(null, 'cfg=some-filter', 100)->andReturnUsing($this->scanMethodHandler());
        $redis->shouldReceive('scan')->once()->with(0, 'cfg=some-filter', 100)->andReturnUsing($this->scanMethodHandler());
        $redis->shouldReceive('scan')->once()->with(1, 'cfg=some-filter', 100)->andReturnUsing($this->scanMethodHandler());

        // Call the scan method
        $method = new \ReflectionMethod(RedisPaginatedTraitFixture::class, 'paginatedScan');
        $method->setAccessible(true);
        $fixture = new RedisPaginatedTraitFixture();
        $result = iterator_to_array($method->invoke($fixture, $redis, 'some-filter'));

        $this->assertEquals(['foo', 'bar', 'baz'], $result);
    }

    public function testPaginatedScanValues()
    {
        $keys = $this->range('keys', 1, 60);
        $values = $this->range('values', 1, 60);

        $chunkedKeys = array_chunk($keys, 50);
        $chunkedValues = array_chunk($values, 50);

        $scanKeys = $chunkedKeys;
        $scanMock = function() use ($keys) {
            yield from $keys;
        };

        $redis = M::mock(Redis::class);
        foreach ($chunkedKeys as $chunk) {
            $redis->shouldReceive('mget')->once()->with($chunk)->andReturn(array_shift($chunkedValues));
        }

        $fixture = new RedisPaginatedTraitValuesFixture($scanMock);
        $method = new \ReflectionMethod(RedisPaginatedTraitValuesFixture::class, 'paginatedScanValues');
        $method->setAccessible(true);
        $result = iterator_to_array($method->invoke($fixture, $redis, 'some-filter'));

        // Make sure we have all 60 items even though they were requested in chunks of 50
        $this->assertEquals(array_combine($keys, $values), $result);
    }

    private function range($prefix, $start, $end)
    {
        $keys = [];
        foreach (range($start, $end) as $key) {
            $keys[] = $prefix . $key;
        }
        return $keys;
    }

    /**
     * Return a spoof scan method that manages increasing the iterator and returning varied results
     *
     * @return \Closure
     */
    private function scanMethodHandler()
    {
        return static function(&$iterator, $search, $batch) {
            $results = [
                ['cfg=foo', 'cfg=bar'],
                ['cfg=baz'],
                false
            ];
            if ($iterator === null) {
                $iterator = -1;
            }

            $iterator++;
            if (isset($results[$iterator])) {
                return $results[$iterator];
            }
        };
    }

}
