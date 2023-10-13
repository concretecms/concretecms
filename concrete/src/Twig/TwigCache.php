<?php

namespace Concrete\Core\Twig;

use Concrete\Core\Cache\Level\ExpensiveCache;
use Twig\Cache\CacheInterface;

class TwigCache implements CacheInterface
{
    public const CACHE_TTL = 86400;

    /**
     * @var ExpensiveCache
     */
    private $cache;

    public function __construct(ExpensiveCache $cache)
    {
        $this->cache = $cache;
    }

    public function generateKey($name, $className): string
    {
        return $className . '/' . $name;
    }

    public function write($key, $content): void
    {
        $cacheItem = $this->cache->getItem($key);
        $cacheItem->set($content)->setTTL(self::CACHE_TTL);
        $this->cache->save($cacheItem);
    }

    /**
     * @param $key
     *
     * @return mixed
     */
    public function load($key)
    {
        return $this->cache->getItem($key)->get();
    }

    public function getTimestamp($key): int
    {
        return $this->cache->getItem($key)->getExpiration()->getTimestamp();
    }
}
