<?php

namespace Concrete\Core\Twig;

use Twig\Cache\CacheInterface;
use Twig\Cache\RemovableCacheInterface;

class TwigCache implements CacheInterface, RemovableCacheInterface
{
    /**
     * @var iterable<CacheInterface>
     */
    private iterable $caches;

    public const CACHE_TTL = 86400;

    /**
     * @param iterable<CacheInterface> $caches
     */
    public function __construct(iterable $caches)
    {
        $this->caches = $caches;
    }

    public function generateKey(string $name, string $className): string
    {
        return $className . '/' . $name;
    }

    public function write(string $key, string $content): void
    {
        foreach ($this->caches as $cache) {
            $cache->write($key, $content);
        }
    }

    public function load(string $key): void
    {
        foreach ($this->caches as $cache) {
            $cache->load($key);
            if (class_exists(explode('/', $key)[0], false)) {
                break;
            }
        }
    }

    public function getTimestamp(string $key): int
    {
        foreach ($this->caches as $cache) {
            $timestamp = $cache->getTimestamp($key);
            if ($timestamp > 0) {
                return $timestamp;
            }
        }

        return 0;
    }

    public function remove(string $name, string $cls): void
    {
        foreach ($this->caches as $cache) {
            if ($cache instanceof RemovableCacheInterface) {
                $cache->remove($name, $cls);
            }
        }
    }
}
