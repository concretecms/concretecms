<?php

namespace Concrete\Core\Config\Driver\Redis;

use Concrete\Core\Config\LoaderInterface;
use Redis;

class RedisLoader implements LoaderInterface
{

    use RedisPaginatedTrait;

    /** @var Redis */
    protected $connection;

    public function __construct(Redis $redis)
    {
        $this->connection = $redis;
    }

    /**
     * @param $namespace
     */
    public function clearNamespace($namespace)
    {
        $keys = $this->paginatedScan($this->connection, "{$namespace}::*");
        if ($keys) {
            $this->connection->del($keys);
        }
    }

    /**
     * Load the given configuration group.
     *
     * @param string $environment
     * @param string $group
     * @param string $namespace
     * @return array
     */
    public function load($environment, $group, $namespace = null)
    {
        $values = [];
        $results = $this->paginatedScanValues($this->connection, "{$namespace}::{$group}.*");

        foreach ($results as $key => $result) {
            list($namespace, $key) = explode('::', $key);
            list($group, $item) = explode('.', $key, 2);

            array_set($values, $item, unserialize($result));
        }

        return $values;
    }

    /**
     * Determine if the given configuration group exists.
     *
     * @param string $group
     * @param string $namespace
     * @return bool
     */
    public function exists($group, $namespace = null)
    {
        foreach ($this->paginatedScan($this->connection, "{$namespace}::{$group}.*") as $key) {
            return true;
        }

        return false;
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param string $namespace
     * @param string $hint
     * @return void
     */
    public function addNamespace($namespace, $hint)
    {
        // We don't actually have to do anything here
        return;
    }

    /**
     * Returns all registered namespaces with the config
     * loader.
     *
     * @return array
     */
    public function getNamespaces()
    {
        $namespaces = [];
        $keys = $this->paginatedScan($this->connection, '*');
        foreach ($keys as $key) {
            list($namespace, $key) = explode('::', $key);
            if ($namespace) {
                $namespaces[$namespace] = 1;
            }
        }

        return array_keys($namespaces);
    }

    /**
     * Apply any cascades to an array of package options.
     *
     * @param string $environment
     * @param string $package
     * @param string $group
     * @param array $items
     * @return array
     */
    public function cascadePackage($environment, $package, $group, $items)
    {
        return $items;
    }
}
