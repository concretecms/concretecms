<?
namespace Concrete\Core\Cache;

use Stash\Pool;

abstract class Cache
{
    /** @var Pool */
    public $pool = null;
    public $enabled = false;

    public function __construct() {
        $this->init();
    }

    /**
     * Initializes the cache by setting up the cache pool and enabling the cache
     * @return void
     */
    abstract protected function init();

    /**
     * Deletes an item from the cache
     * @param string $key Name of the cache item ID
     * @return bool True if deleted, false if not
     */
    public function delete($key)
    {
        return $this->pool->getItem($key)->clear();
    }

    /**
     * Checks if an item exists in the cache
     * @param string $key Name of the cache item ID
     * @return bool True if exists, false if not
     */
    public function exists($key)
    {
        return !$this->pool->getItem()->isMiss($key);
    }

    /**
     * Removes all values from the cache
     */
    public function flush()
    {
        return $this->pool->flush();
    }

    /**
     * Gets a value from the cache
     * @param string $key Name of the cache item ID
     * @return \Stash\Interfaces\ItemInterface
     */
    public function getItem($key)
    {
        return $this->pool->getItem($key);
    }

    public function enable()
    {
        $this->enabled = true;
    }

    public function disable()
    {
        $this->enabled = false;
    }

    public function isEnabled()
    {
        return $this->enabled;
    }
}
