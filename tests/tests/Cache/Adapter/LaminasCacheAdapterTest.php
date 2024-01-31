<?php

namespace Concrete\Tests\Cache\Adapter;

use Concrete\Tests\TestCase;
use Mockery;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Cache\CacheItem;

class LaminasCacheAdapterTest extends TestCase
{

    public function testGet(): void
    {
        $item = Mockery::mock(CacheItemInterface::class);
        $item->expects('isHit')->andReturnTrue();
        $item->expects('get')->andReturn('baz');

        $success = null;
        $cache = Mockery::mock(CacheItemPoolInterface::class);
        $cache->expects('getItem')->twice()->with('foo')->andReturn($item);

        $adapter = new \Concrete\Core\Cache\Adapter\LaminasCacheAdapter($cache);
        $this->assertEquals('baz', $adapter->getItem('foo', $success));
        $this->assertTrue($success);

        $item->expects('isHit')->andReturn(false);
        $adapter = new \Concrete\Core\Cache\Adapter\LaminasCacheAdapter($cache);
        $this->assertNull($adapter->getItem('foo', $success));
        $this->assertFalse($success);
    }

    public function testSet(): void
    {
        $item = new CacheItem();

        $cache = Mockery::mock(CacheItemPoolInterface::class);
        $cache->shouldReceive('getItem')->with('foo')->andReturn($item);
        $cache->expects('save')->with($item)->andReturn(true);

        $adapter = new \Concrete\Core\Cache\Adapter\LaminasCacheAdapter($cache);
        $this->assertTrue($adapter->setItem('foo', 123));
        $this->assertEquals(123, $item->get());

        $cache->expects('save')->with($item)->andReturn(false);
        $this->assertFalse($adapter->setItem('foo', 12345));
        $this->assertEquals(12345, $item->get());

        // Test TTL
        $adapter->setOptions(['ttl' => 1337]);
        $item = Mockery::spy(CacheItemInterface::class);
        $item->shouldReceive('set')->andReturnSelf();
        $item->expects('expiresAfter')->with(1337)->andReturnSelf();

        $cache->shouldReceive('getItem', 'baz')->andReturn($item);
        $cache->expects('save')->with($item)->andReturn(true);

        $adapter->setItem('baz', 123);
    }

    public function testDelete(): void
    {
        $cache = Mockery::mock(CacheItemPoolInterface::class);
        $cache->expects('deleteItem')->with('foo')->andReturnTrue();

        $adapter = new \Concrete\Core\Cache\Adapter\LaminasCacheAdapter($cache);
        $this->assertTrue($adapter->removeItem('foo'));

        $cache->expects('deleteItem')->with('foo')->andReturnFalse();
        $this->assertFalse($adapter->removeItem('foo'));
    }

}