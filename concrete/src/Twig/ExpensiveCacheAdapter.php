<?php

namespace Concrete\Core\Twig;

use Concrete\Core\Cache\Level\ExpensiveCache;
use Twig\Cache\CacheInterface;

class ExpensiveCacheAdapter implements CacheInterface
{
    private ExpensiveCache $expensiveCache;

    public function __construct(ExpensiveCache $expensiveCache)
    {
        $this->expensiveCache = $expensiveCache;
    }

    public function generateKey(string $name, string $className): string
    {
        return $className . '/' . $name;
    }

    public function write(string $key, string $content): void
    {
        $cacheItem = $this->expensiveCache->getItem($key);
        $cacheItem->set($content)->expiresAfter(TwigCache::CACHE_TTL);
        $this->expensiveCache->save($cacheItem);
    }

    public function load(string $key): void
    {
        $cacheItem = $this->expensiveCache->getItem($key);
        $cacheItem->get();
    }

    public function getTimestamp(string $key): int
    {
        $cacheItem = $this->expensiveCache->getItem($key);
        return $cacheItem->getExpiration()->getTimestamp();
    }
}
