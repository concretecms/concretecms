<?php
namespace Concrete\Core\Cache;

use Concrete\Core\Support\Facade\Application;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;

/**
 * Base class for the three caching layers present in Concrete5:
 *   - ExpensiveCache
 *   - ObjectCache
 *   - RequestCache
 *
 * Cache storage is performed using the Stash Library, see http://www.stashphp.com/
 *
 * This class imports the various caching settings from Config class, sets
 * up the Stash pools and provides a basic caching API for all of Concrete5.
 *
 * @template T of mixed
 */
abstract class Cache implements CacheItemPoolInterface, FlushableInterface
{
    protected bool $enabled = false;

    public function __construct(
        public readonly CacheItemPoolInterface $pool
    ) {}

    /**
     * @deprecated Interact with `->pool` directly
     */
    public function getPool(): CacheItemPoolInterface
    {
        return $this->pool;
    }

    /**
     * Deletes an item from the cache.
     *
     * @param string $key Name of the cache item ID
     *
     * @return bool True if deleted, false if not
     */
    public function delete(string $key): bool
    {
        if (!$this->enabled) {
            return false;
        }

        return $this->pool->deleteItem($this->normalize($key));
    }

    /**
     * Checks if an item exists in the cache.
     *
     * @param string $key Name of the cache item ID
     *
     * @return bool True if exists, false if not
     * @throws InvalidArgumentException
     */
    public function exists(string $key): bool
    {

        if (!$this->enabled) {
            return false;
        }

        return !$this->pool->hasItem($this->normalize($key));
    }

    /**
     * Removes all values from the cache.
     */
    public function flush(): bool
    {
        return $this->pool->clear();
    }

    /**
     * Gets a value from the cache.
     *
     * @param string $key Name of the cache item ID
     *
     * @return CacheItemProxy<T>
     * @throws InvalidArgumentException
     */
    public function getItem($key): CacheItemInterface
    {
        $item = $this->pool->getItem($this->normalize($key));
        if ($item instanceof CacheItemProxy) {
            return $item;
        }

        return new CacheItemProxy($item);
    }

    public function save(CacheItemInterface $item): bool
    {
        return $this->pool->save($item);
    }

    /**
     * Enables the cache.
     */
    public function enable(): void
    {
        $this->enabled = true;
    }

    /**
     * Disables the cache.
     */
    public function disable(): void
    {
        $this->enabled = false;
    }

    /**
     * Returns true if the cache is enabled, false if not.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    /**
     * Disables all cache levels.
     */
    public static function disableAll(): void
    {
        $app = Application::getFacadeApplication();
        $app->make('cache/request')?->disable();
        $app->make('cache/expensive')?->disable();
        $app->make('cache')?->disable();
    }

    /**
     * Enables all cache levels.
     */
    public static function enableAll(): void
    {
        $app = Application::getFacadeApplication();
        $app->make('cache/request')?->enable();
        $app->make('cache/expensive')?->enable();
        $app->make('cache')?->enable();
    }

    private function normalize(string $key): string
    {
        return str_replace(
            ['{', '}', '(', ')', '/', '\\', '@', ':'],
            ['†', '‡', '‹', '›', '™', '•', 'œ', 'Ÿ'],
            $key,
        );
    }

    public function getItems(array $keys = array()): array
    {
        return $this->pool->getItems(array_map($this->normalize(...), $keys));
    }

    public function hasItem($key): bool
    {
        return $this->pool->hasItem($this->normalize($key));
    }

    public function clear(): bool
    {
        return $this->pool->clear();
    }

    public function deleteItem($key): bool
    {
        return $this->pool->deleteItem($this->normalize($key));
    }

    public function deleteItems(array $keys): bool
    {
        return $this->pool->deleteItems(array_map($this->normalize(...), $keys));
    }

    public function saveDeferred(CacheItemInterface $item): bool
    {
        return $this->pool->saveDeferred($item);
    }

    public function commit(): bool
    {
        return $this->pool->commit();
    }
}
