<?php

namespace Concrete\Core\Config\Repository;

use Closure;
use Concrete\Core\Config\LoaderInterface;
use Concrete\Core\Config\SaverInterface;

class Repository extends \Illuminate\Config\Repository
{
    /**
     * The loader implementation.
     *
     * @var \Concrete\Core\Config\LoaderInterface
     */
    protected $loader;

    /**
     * The saver implementation.
     *
     * @var \Concrete\Core\Config\SaverInterface
     */
    protected $saver;

    /**
     * The current environment.
     *
     * @var string
     */
    protected $environment;

    /**
     * All of the configuration items.
     *
     * @var array
     */
    protected $items = [];

    /**
     * All of the registered packages.
     *
     * @var array
     */
    protected $packages = [];

    /**
     * The after load callbacks for namespaces.
     *
     * @var array
     */
    protected $afterLoad = [];

    /**
     * A cache of the parsed items.
     *
     * @var array
     */
    protected $parsed = [];

    /**
     * Create a new configuration repository.
     *
     * @param \Concrete\Core\Config\LoaderInterface $loader
     * @param \Concrete\Core\Config\SaverInterface $saver
     * @param string $environment
     */
    public function __construct(LoaderInterface $loader, SaverInterface $saver, $environment)
    {
        $this->loader = $loader;
        $this->saver = $saver;
        $this->environment = $environment;
    }

    /**
     * Determine if the given configuration value exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        $default = microtime(true);

        return $this->get($key, $default) !== $default;
    }

    /**
     * Determine if a configuration group exists.
     *
     * @param string $key
     *
     * @return bool
     */
    public function hasGroup($key)
    {
        list($namespace, $group) = $this->parseKey($key);

        return $this->loader->exists($group, $namespace);
    }

    /**
     * Get the specified configuration value.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        list($namespace, $group, $item) = $this->parseKey($key);

        // Configuration items are actually keyed by "collection", which is simply a
        // combination of each namespace and groups, which allows a unique way to
        // identify the arrays of configuration items for the particular files.
        $collection = $this->getCollection($group, $namespace);

        $this->load($group, $namespace, $collection);

        return array_get($this->items[$collection], $item, $default);
    }

    /**
     * Set a given configuration value.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value = null)
    {
        list($namespace, $group, $item) = $this->parseKey($key);

        $collection = $this->getCollection($group, $namespace);

        // We'll need to go ahead and lazy load each configuration groups even when
        // we're just setting a configuration item so that the set item does not
        // get overwritten if a different item in the group is requested later.
        $this->load($group, $namespace, $collection);

        if ($item === null) {
            $this->items[$collection] = $value;
        } else {
            array_set($this->items[$collection], $item, $value);
        }
    }

    /**
     * Save a key.
     *
     * @param string $key
     * @param mixed $value
     *
     * @return bool
     */
    public function save($key, $value)
    {
        list($namespace, $group, $item) = $this->parseKey($key);
        $collection = $this->getCollection($group, $namespace);
        unset($this->items[$collection]);

        if ($this->saver->save($item, $value, $this->environment, $group, $namespace)) {
            $this->load($group, $namespace, $this->getCollection($group, $namespace));

            return true;
        }

        return false;
    }

    /**
     * Clear specific key.
     *
     * @param string $key
     */
    public function clear($key)
    {
        $this->set($key, null);
    }

    /**
     * Clear cached items.
     */
    public function clearCache()
    {
        $this->items = [];
    }

    /**
     * Clear a namespace (Note: this deletes items permanently).
     *
     * @param string $namespace
     */
    public function clearNamespace($namespace)
    {
        $this->loader->clearNamespace($namespace);
    }

    /**
     * Register a package for cascading configuration.
     *
     * @param string $package
     * @param string|null $hint
     * @param string|null $namespace
     */
    public function package($package, $hint = null, $namespace = null)
    {
        $namespace = $this->getPackageNamespace($package, $namespace);
        $hint = $hint ? $hint : $package->getPackagePath() . '/' . DIRNAME_CONFIG;
        $this->packages[] = $namespace;
        $this->addNamespace($namespace, $hint);
    }

    /**
     * Register an after load callback for a given namespace.
     *
     * @param string $namespace
     * @param \Closure $callback
     */
    public function afterLoading($namespace, Closure $callback)
    {
        $this->afterLoad[$namespace] = $callback;
    }

    /**
     * Add a new namespace to the loader.
     *
     * @param string $namespace
     * @param string $hint
     */
    public function addNamespace($namespace, $hint)
    {
        $this->loader->addNamespace($namespace, $hint);
    }

    /**
     * Returns all registered namespaces with the config
     * loader.
     *
     * @return array
     */
    public function getNamespaces()
    {
        return $this->loader->getNamespaces();
    }

    /**
     * Get the loader implementation.
     *
     * @return LoaderInterface
     */
    public function getLoader()
    {
        return $this->loader;
    }

    /**
     * Set the loader implementation.
     *
     * @param LoaderInterface $loader
     */
    public function setLoader(LoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    /**
     * Get the saver implementation.
     *
     * @return SaverInterface
     */
    public function getSaver()
    {
        return $this->saver;
    }

    /**
     * Set the saver instance.
     *
     * @param SaverInterface $saver
     */
    public function setSaver(SaverInterface $saver)
    {
        $this->saver = $saver;
    }

    /**
     * Get the current configuration environment.
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->environment;
    }

    /**
     * Get the after load callback array.
     *
     * @return array
     */
    public function getAfterLoadCallbacks()
    {
        return $this->afterLoad;
    }

    /**
     * Get all of the configuration items.
     *
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Parse a key into namespace, group, and item.
     *
     * @param string $key
     *
     * @return array
     */
    public function parseKey($key)
    {
        // If we've already parsed the given key, we'll return the cached version we
        // already have, as this will save us some processing. We cache off every
        // key we parse so we can quickly return it on all subsequent requests.
        if (isset($this->parsed[$key])) {
            return $this->parsed[$key];
        }

        // If the key does not contain a double colon, it means the key is not in a
        // namespace, and is just a regular configuration item. Namespaces are a
        // tool for organizing configuration items for things such as modules.
        if (strpos($key, '::') === false) {
            $segments = explode('.', $key);

            $parsed = $this->parseBasicSegments($segments);
        } else {
            $parsed = $this->parseNamespacedSegments($key);
        }

        // Once we have the parsed array of this key's elements, such as its groups
        // and namespace, we will cache each array inside a simple list that has
        // the key and the parsed array for quick look-ups for later requests.
        return $this->parsed[$key] = $parsed;
    }

    /**
     * Set the parsed value of a key.
     *
     * @param string $key
     * @param array $parsed
     */
    public function setParsedKey($key, $parsed)
    {
        $this->parsed[$key] = $parsed;
    }

    /**
     * Execute a callable using a specific key value.
     *
     * @param string $key
     * @param mixed $value
     * @param callable $callable
     *
     * @return mixed returns the result of $callable
     */
    public function withKey($key, $value, callable $callable)
    {
        $initialValue = $this->get($key);
        try {
            $this->set($key, $value);

            return $callable();
        } finally {
            $this->set($key, $initialValue);
        }
    }

    /**
     * Load the configuration group for the key.
     *
     * @param string $group
     * @param string $namespace
     * @param string $collection
     */
    protected function load($group, $namespace, $collection)
    {
        $env = $this->environment;

        // If we've already loaded this collection, we will just bail out since we do
        // not want to load it again. Once items are loaded a first time they will
        // stay kept in memory within this class and not loaded from disk again.
        if (isset($this->items[$collection])) {
            return;
        }

        $items = $this->loader->load($env, $group, $namespace);

        // If we've already loaded this collection, we will just bail out since we do
        // not want to load it again. Once items are loaded a first time they will
        // stay kept in memory within this class and not loaded from disk again.
        if (isset($this->afterLoad[$namespace])) {
            $items = $this->callAfterLoad($namespace, $group, $items);
        }

        $this->items[$collection] = $items;
    }

    /**
     * Call the after load callback for a namespace.
     *
     * @param string $namespace
     * @param string $group
     * @param array $items
     *
     * @return array
     */
    protected function callAfterLoad($namespace, $group, $items)
    {
        $callback = $this->afterLoad[$namespace];

        return call_user_func($callback, $this, $group, $items);
    }

    /**
     * Parse an array of namespaced segments.
     *
     * @param string $key
     *
     * @return array
     */
    protected function parseNamespacedSegments($key)
    {
        list($namespace, $item) = explode('::', $key);

        // If the namespace is registered as a package, we will just assume the group
        // is equal to the namespace since all packages cascade in this way having
        // a single file per package, otherwise we'll just parse them as normal.
        if (in_array($namespace, $this->packages)) {
            return $this->parsePackageSegments($key, $namespace, $item);
        }

        // Next we'll just explode the first segment to get the namespace and group
        // since the item should be in the remaining segments. Once we have these
        // two pieces of data we can proceed with parsing out the item's value.
        $itemSegments = explode('.', $item);

        $groupAndItem = array_slice($this->parseBasicSegments($itemSegments), 1);

        return array_merge([$namespace], $groupAndItem);
    }

    /**
     * @param string $key
     * @param string $namespace
     * @param string $item
     *
     * @return array
     */
    protected function parsePackageSegments($key, $namespace, $item)
    {
        list($namespace, $item) = explode('::', $key);

        // First we'll just explode the first segment to get the namespace and group
        // since the item should be in the remaining segments. Once we have these
        // two pieces of data we can proceed with parsing out the item's value.
        $itemSegments = explode('.', $item);

        $groupAndItem = array_slice($this->parseBasicSegments($itemSegments), 1);

        return array_merge([$namespace], $groupAndItem);
    }

    /**
     * Get the configuration namespace for a package.
     *
     * @param string|\Concrete\Core\Package\Package $package
     * @param string $namespace
     *
     * @return string
     */
    protected function getPackageNamespace($package, $namespace)
    {
        $package = is_object($package) ? $package->getPackageHandle() : $package;

        return $namespace ?: $package;
    }

    /**
     * Get the collection identifier.
     *
     * @param string $group
     * @param string $namespace
     *
     * @return string
     */
    protected function getCollection($group, $namespace = null)
    {
        $namespace = $namespace ?: '*';

        return $namespace . '::' . $group;
    }

    /**
     * Parse an array of basic segments.
     *
     * @param array $segments
     *
     * @return array
     */
    protected function parseBasicSegments(array $segments)
    {
        // The first segment in a basic array will always be the group, so we can go
        // ahead and grab that segment. If there is only one total segment we are
        // just pulling an entire group out of the array and not a single item.
        $group = $segments[0];

        if (count($segments) == 1) {
            return [null, $group, null];
        }

        // If there is more than one segment in this group, it means we are pulling
        // a specific item out of a groups and will need to return the item name
        // as well as the group so we know which item to pull from the arrays.
        else {
            $item = implode('.', array_slice($segments, 1));

            return [null, $group, $item];
        }
    }
}
