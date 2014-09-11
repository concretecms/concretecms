<?php
namespace Concrete\Core\Cache;

use Core;
use Stash\Driver\BlackHole;
use Stash\Driver\Composite;
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
     * Loads the composite driver from constants
     * @param $level
     * @return \Stash\Interfaces\DriverInterface
     */
    protected function loadConfig($level)
    {
        $drivers = [];
        $driversBuild = [];

        $constants = array_keys(get_defined_constants());

        foreach ($constants as $constant) {
            if (preg_match('/^CACHE_' . strtoupper($level) . '(?:_([0-9]+))?_DRIVER$/', $constant, $matches) === 1) {
                if (isset($matches[1])) {
                    $index = $matches[1];
                } else {
                    $index = 0;
                }
                $driversBuild[$index]['driver'] = constant($constant);
            } elseif (preg_match('/^CACHE_' . strtoupper($level) . '(?:_([0-9]+))?_OPTION_(.*)$/', $constant, $matches) === 1) {
                if (isset($matches[1])) {
                    $index = $matches[1];
                } else {
                    $index = 0;
                }

                $options = explode('__', $matches[2]);

                if (!isset($driversBuild[$index])) {
                    $driversBuild[$index] = array();
                }

                if (!isset($driversBuild[$index]['options'])) {
                    $driversBuild[$index]['options'] = array();
                }

                $optionsBuild = &$driversBuild[$index]['options'];
                for ($i = 0; $i < count($options); $i++) {
                    if (!isset($optionsBuild[$options[$i]])) {
                        $optionsBuild[$options[$i]] = array();
                    }
                    $optionsBuild = &$optionsBuild[$options[$i]];
                }
                $optionsBuild = constant($constant);
            }
        }

        ksort($driversBuild);

        foreach ($driversBuild as $driverBuild) {
            $tempDriver = new $driverBuild['driver']();
            if (isset($driverBuild['options'])) {
                $tempDriver->setOptions($driverBuild['options']);
            }

            $drivers[] = $tempDriver;
        }

        $numDrivers = count($drivers);
        if ($numDrivers > 1) {
            $driver = new Composite();
            $driver->setOptions(array('drivers' => $drivers));
        } elseif ($numDrivers === 1) {
            $driver = $drivers[0];
        } else {
            $driver = new BlackHole();
        }

        return $driver;
    }

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

    /**
     * Disables all cache levels
     */
    public static function disableAll()
    {
        Core::make('cache/request')->disable();
        Core::make('cache/expensive')->disable();
        Core::make('cache')->disable();
    }

    /**
     * Enables all cache levels
     */
    public static function enableAll()
    {
        Core::make('cache/request')->enable();
        Core::make('cache/expensive')->enable();
        Core::make('cache')->enable();
    }
}
