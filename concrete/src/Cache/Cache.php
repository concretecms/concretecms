<?php
namespace Concrete\Core\Cache;

use Concrete\Core\Support\Facade\Application;
use Psr\Cache\CacheItemInterface;
use Stash\Driver\BlackHole;
use Stash\Driver\Composite;
use Stash\Pool;

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
 */
abstract class Cache implements FlushableInterface
{
    /** @var Pool */
    public $pool = null;
    /** @var bool */
    protected $enabled = false;
    /** @var \Stash\Interfaces\DriverInterface */
    protected $driver = null;

    public function __construct()
    {
        $this->init();
    }

    /**
     * Initializes the cache by setting up the cache pool and enabling the cache.
     */
    abstract protected function init();

    /**
     * Loads the composite driver from constants.
     *
     * @param $level
     *
     * @return \Stash\Interfaces\DriverInterface
     */
    protected function loadConfig($level)
    {
        $app = Application::getFacadeApplication();
        $drivers = [];
        $driverConfigs = $app['config']->get("concrete.cache.levels.{$level}.drivers", []);
        $preferredDriverName = $app['config']->get("concrete.cache.levels.{$level}.preferred_driver", null);

        // Load the preferred driver(s) first
        if (!empty($preferredDriverName)) {
            if (is_array($preferredDriverName)) {
                foreach ($preferredDriverName as $driverName) {
                    $preferredDriver = array_get($driverConfigs, $driverName, []);
                    $drivers[] = $this->buildDriver($preferredDriver);
                }
            } else {
                $preferredDriver = array_get($driverConfigs, $preferredDriverName, []);
                $drivers[] = $this->buildDriver($preferredDriver);
            }
        }
        // If we dont have any perferred drivers or preferred drivers available
        // Build Everything
        if (empty($drivers)) {
            foreach ($driverConfigs as $driverConfig) {
                if (!$driverConfig) {
                    continue;
                }

                $drivers[] = $this->buildDriver($driverConfig);
            }
        }

        // Remove any empty arrays for an accurate count
        array_filter($drivers);
        $count = count($drivers);
        if ($count > 1) {
            $driver = new Composite(['drivers' => $drivers]);
        } elseif ($count === 1) {
            reset($drivers);
            $driver = current($drivers);
        } else {
            $driver = new BlackHole();
        }

        return $driver;
    }

    /**
     * Function used to build a driver from a driverConfig array.
     *
     * @param array $driverConfig The config item belonging to the driver
     *
     * @return null|\Stash\Interfaces\DriverInterface
     */
    private function buildDriver(array $driverConfig)
    {
        $class = array_get($driverConfig, 'class', '');
        if ($class && class_exists($class)) {
            $implements = class_implements($class);

            // Make sure that the provided class implements the DriverInterface
            if (isset($implements['Stash\Interfaces\DriverInterface'])) {
                /* @var \Stash\Interfaces\DriverInterface $tempDriver */

                // Only add if the driver is available
                if ($class::isAvailable()) {
                    $tempDriver = new $class(array_get($driverConfig, 'options', null));

                    return $tempDriver;
                }
            } else {
                throw new \RuntimeException('Cache driver class must implement \Stash\Interfaces\DriverInterface.');
            }
        }

        return null;
    }

    /**
     * Deletes an item from the cache.
     *
     * @param string $key Name of the cache item ID
     *
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
     * Checks if an item exists in the cache.
     *
     * @param string $key Name of the cache item ID
     *
     * @return bool True if exists, false if not
     */
    public function exists($key)
    {
        if ($this->enabled) {
            return !$this->pool->getItem($key)->isMiss();
        } else {
            return false;
        }
    }

    /**
     * Removes all values from the cache.
     */
    public function flush()
    {
        return $this->pool->clear();
    }

    /**
     * Gets a value from the cache.
     *
     * @param string $key Name of the cache item ID
     *
     * @return \Stash\Interfaces\ItemInterface
     */
    public function getItem($key)
    {
        return $this->pool->getItem($key);
    }

    public function save(CacheItemInterface $item)
    {
        return $this->pool->save($item);
    }

    /**
     * Enables the cache.
     */
    public function enable()
    {
        if ($this->driver !== null) {
            $this->pool->setDriver($this->driver);
        }
        $this->enabled = true;
    }

    /**
     * Disables the cache.
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
     * Returns true if the cache is enabled, false if not.
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->enabled;
    }

    /**
     * Disables all cache levels.
     */
    public static function disableAll()
    {
        $app = Application::getFacadeApplication();
        $app->make('cache/request')->disable();
        $app->make('cache/expensive')->disable();
        $app->make('cache')->disable();
    }

    /**
     * Enables all cache levels.
     */
    public static function enableAll()
    {
        $app = Application::getFacadeApplication();
        $app->make('cache/request')->enable();
        $app->make('cache/expensive')->enable();
        $app->make('cache')->enable();
    }
}
