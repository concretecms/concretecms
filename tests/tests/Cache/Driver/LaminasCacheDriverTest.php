<?php

namespace Concrete\Tests\Cache\Driver;

use Concrete\Core\Cache\Adapter\LaminasCacheDriver;
use Concrete\Core\Cache\Level\RequestCache;
use Concrete\Core\Support\Facade\Application;
use Concrete\Tests\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class LaminasCacheDriverTest extends TestCase
{
    public function testGetLaminasCacheItem()
    {
        $app = Application::getFacadeApplication();
        $cache = 'cache/' . uuid_create();

        $mockItem = \Mockery::mock(CacheItemInterface::class);
        $mockItem->shouldReceive('get')->andReturn('example');
        $mockItem->shouldReceive('isMiss')->andReturn(false);
        $mockCache = \Mockery::mock(CacheItemPoolInterface::class);
        $mockCache->shouldReceive('getItem')->with('laminas/test/key')->andReturn($mockItem);
        $app->instance($cache, $mockCache);

        $driver = new LaminasCacheDriver($cache, 50);
        $this->assertEquals('example', $driver->getItem('test/key'));
    }

    public function testSetLaminasCacheItem()
    {
        $key = 'test/set';
        $value = 'example';
        $cacheName = 'cache/request';
        $now = time();
        $driver = new LaminasCacheDriver($cacheName, 50);
        $result = $driver->setItem($key, $value);
        if ($result) {
            $app = Application::getFacadeApplication();
            /** @var RequestCache $cache */
            $cache = $app->make($cacheName);
            $item = $cache->getItem('laminas/' . $key);
            if (!$item->isMiss()) {
                $expires = $item->getExpiration()->getTimestamp();
                $this->assertGreaterThanOrEqual($now, $expires);
                $this->assertLessThan($now + 100, $expires);
                $this->assertEquals($value, $item->get());
                $item->clear();
            } else {
                $this->fail('Failed to get cache item');
            }
        } else {
            $this->fail('Failed to set cache item');
        }
    }

    public function testRemoveLaminasCacheItem()
    {
        $key = 'test/remove';
        $value = 'example';
        $cacheName = 'cache/request';
        $app = Application::getFacadeApplication();
        /** @var RequestCache $cache */
        $cache = $app->make($cacheName);
        $item = $cache->getItem('laminas/' . $key);
        $item->set($value);
        $cache->save($item);

        $driver = new LaminasCacheDriver($cacheName, 50);
        $driver->removeItem($key);

        $item = $cache->getItem('zend/' . $key);
        $this->assertFalse($item->isHit());
    }
}
