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

    public static function setUpBeforeClass()
    {
        require_once __DIR__ . '/Fixtures/Redis.php';
    }

    public function testPaginatedScan()
    {
        M::getConfiguration()->setInternalClassMethodParamMap('Redis', 'scan', [
            '&$iterator',
            '$pattern = null',
            '$count = 0'
        ]);

        $redis = M::mock('Redis');
        $expectedIterators = [null, 127, 135, 205];
        $returnValues = [['cfg=foo', 'cfg=bar'], ['cfg=baz'], false];

        $redis->shouldReceive('scan')->times(3)->with(
            M::on(function(&$iterator) use (&$expectedIterators) {
                $expected = array_shift($expectedIterators);

                if ($iterator === $expected) {
                    $iterator = head($expectedIterators);
                    return true;
                }

                return false;
            }),
            'cfg=some-filter',
            100
        )->andReturnValues($returnValues);

        // Call the scan method
        $method = new \ReflectionMethod(RedisPaginatedTraitFixture::class, 'paginatedScan');
        $method->setAccessible(true);
        $fixture = new RedisPaginatedTraitFixture();
        $result = iterator_to_array($method->invoke($fixture, $redis, 'some-filter'));

        $this->assertEquals(['foo', 'bar', 'baz'], $result);
        M::resetContainer();
    }

    public function testPaginatedScanValues()
    {
        $keys = $this->range('keys', 1, 60);
        $values = $this->range('values', 1, 60);

        $chunkedKeys = array_chunk($keys, 50);
        $chunkedValues = array_chunk($values, 50);
        $scanMock = function() use ($keys) {
            foreach ($keys as $key) {
                yield $key;
            }
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
}
