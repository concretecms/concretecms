<?
namespace Concrete\Core\Cache;

use Stash\Driver\Composite;
use Stash\Driver\Ephemeral;
use Stash\Driver\FileSystem;
use Stash\Pool;

class Cache
{
    /** @var Pool */
    private $pool = null;

    /**
     * Returns the cache pool. If the cache pool is not yet initialized it is initialized.
     * @return Pool
     */
    public function getPool()
    {
        if ($this->pool === null) {
            // cache pool has not yet been initialized
            // todo make this configurable
            $drivers = array();
            $drivers[] = new Ephemeral();
            $drivers[] = new FileSystem();

            $driver = new Composite(array('drivers' => $drivers));

            $this->pool = new Pool($driver);
        }

        return $this->pool;
    }

    /**
     * Deletes an item from the cache
     * @param string $key Name of the cache item ID
     * @return bool True if deleted, false if not
     */
    public function delete($key)
    {
        return $this->getPool()->getItem($key)->clear();
    }

    /**
     * Checks if an item exists in the cache
     * @param string $key Name of the cache item ID
     * @return bool True if exists, false if not
     */
    public function exists($key)
    {
        return !$this->getPool()->getItem()->isMiss($key);
    }

    /**
     * Removes all values from the cache
     */
    public function flush()
    {
        return $this->getPool()->flush();
    }

    /**
     * Gets a value from the cache
     * @param string $key Name of the cache item ID
     * @return \Stash\Interfaces\ItemInterface
     */
    public function getItem($key)
    {
        return $this->getPool()->getItem($key);
    }
}
