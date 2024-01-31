<?php

namespace Concrete\Core\Cache;

use Psr\Cache\CacheItemInterface;

/**
 * @template T
 */
class CacheItemProxy implements CacheItemInterface
{

    public function __construct(
        public readonly CacheItemInterface $cacheItem
    ) {}

    public function getKey()
    {
        return $this->cacheItem->getKey();
    }

    /**
     * @return T
     */
    public function get()
    {
        return $this->cacheItem->get();
    }

    public function isHit()
    {
        return $this->cacheItem->isHit();
    }

    /**
     * @deprecated Use ->isHit())
     */
    final public function isMiss(): bool
    {
        return !$this->isHit();
    }

    /**
     * @Deprecated Lock has been removed with no replacement needed.
     */
    final public function lock(): bool
    {
        return true;
    }

    /**
     * @param T $value
     */
    public function set($value)
    {
        return $this->cacheItem->set($value);
    }

    public function expiresAt($expiration)
    {
        return $this->cacheItem->expiresAt($expiration);
    }

    public function expiresAfter($time)
    {
        return $this->cacheItem->expiresAfter($time);
    }

    public function __call($method, $args)
    {
        $this->cacheItem->{$method}(...$args);
    }
}