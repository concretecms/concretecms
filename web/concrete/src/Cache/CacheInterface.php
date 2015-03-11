<?php


namespace Concrete\Core\Cache;


interface CacheInterface
{

    /**
     * Deletes an item from the cache
     * @param string $key Name of the cache item ID
     * @return bool True if deleted, false if not
     */
    public function delete($key);

    /**
     * Checks if an item exists in the cache
     * @param string $key Name of the cache item ID
     * @return bool True if exists, false if not
     */
    public function exists($key);

    /**
     * Removes all values from the cache
     */
    public function flush();

    /**
     * Gets a value from the cache
     * @param string $key Name of the cache item ID
     * @return \Stash\Interfaces\ItemInterface
     */
    public function getItem($key);

    /**
     * Enables the cache
     */
    public function enable();

    /**
     * Disables the cache
     */
    public function disable();

    /**
     * Returns true if the cache is enabled, false if not
     * @return bool
     */
    public function isEnabled();

    /**
     * Disables all cache levels
     */
    public static function disableAll();

    /**
     * Enables all cache levels
     */
    public static function enableAll();

}
