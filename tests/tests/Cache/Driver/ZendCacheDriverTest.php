<?php

namespace Concrete\Tests\Cache\Driver;

use Concrete\Core\Cache\Adapter\ZendCacheDriver;
use Concrete\Core\Cache\Level\RequestCache;
use Concrete\Core\Support\Facade\Application;

class ZendCacheDriverTest extends \PHPUnit_Framework_TestCase
{
    public function testGetZendCacheItem()
    {
        $key = 'test/get';
        $value = 'example';
        $cacheName = 'cache/request';
        $app = Application::getFacadeApplication();
        /** @var RequestCache $cache */
        $cache = $app->make($cacheName);
        $item = $cache->getItem('zend/' . $key);
        $item->set($value);
        $cache->save($item);

        $driver = new ZendCacheDriver($cacheName, 50);
        $result = $driver->getItem($key);
        $this->assertEquals($value, $result);

        $item->clear();
    }

    public function testSetZendCacheItem()
    {
        $key = 'test/set';
        $value = 'example';
        $cacheName = 'cache/request';
        $now = time();
        $driver = new ZendCacheDriver($cacheName, 50);
        $result = $driver->setItem($key, $value);
        if ($result) {
            $app = Application::getFacadeApplication();
            /** @var RequestCache $cache */
            $cache = $app->make($cacheName);
            $item = $cache->getItem('zend/' . $key);
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

    public function testRemoveZendCacheItem()
    {
        $key = 'test/remove';
        $value = 'example';
        $cacheName = 'cache/request';
        $app = Application::getFacadeApplication();
        /** @var RequestCache $cache */
        $cache = $app->make($cacheName);
        $item = $cache->getItem('zend/' . $key);
        $item->set($value);
        $cache->save($item);

        $driver = new ZendCacheDriver($cacheName, 50);
        $driver->removeItem($key);

        $item = $cache->getItem('zend/' . $key);
        $this->assertFalse($item->isHit());
    }
}
