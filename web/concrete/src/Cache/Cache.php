<?php
namespace Concrete\Core\Cache;

use Stash\Driver\BlackHole;
use Stash\Pool;

abstract class Cache
{
    /** @var Pool */
    protected $pool = null;
    /** @var bool */
    protected $enabled = false;
    /** @var \Stash\Interfaces\DriverInterface */
    protected $driver = null;

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
        if ($this->enabled) {
            return $this->pool->getItem($key)->clear();
        } else {
            return false;
        }
    }

    /**
     * Checks if an item exists in the cache
     * @param string $key Name of the cache item ID
     * @return bool True if exists, false if not
     */
    public function exists($key)
    {
        if ($this->enabled) {
            return !$this->pool->getItem()->isMiss($key);
        } else {
            return false;
        }
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

    /**
     * Enables the cache
     */
    public function enable()
    {
        if ($this->driver !== null) {
            $this->pool->setDriver($this->driver);
        }
        $this->enabled = true;
    }

    /**
     * Disables the cache
     */
    public function disable()
    {
        // save the current driver if not yet black hole so it can be restored on enable()
        if (!($this->pool->getDriver() instanceof BlackHole)) {
            $this->driver = $this->pool->getDriver();
        }
        $this->pool->setDriver(new BlackHole());
        $this->enabled = false;
    }

    /**
     * Returns true if the cache is enabled, false if not
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }
}
