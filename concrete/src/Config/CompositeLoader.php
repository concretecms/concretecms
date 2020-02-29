<?php

namespace Concrete\Core\Config;

use Concrete\Core\Application\Application;

/**
 * A loader that delegates method calls to multiple other loaders
 */
class CompositeLoader implements LoaderInterface
{

    protected $app;

    protected $loaders = [];

    protected $processed = false;

    /**
     * @param LoaderInterface[] $loaders The loaders to delegate to, delegated methods are called in order
     */
    public function __construct(Application $app, array $loaders)
    {
        $this->loaders = $loaders;
        $this->app = $app;
    }

    /**
     * @param $namespace
     */
    public function clearNamespace($namespace)
    {
        foreach ($this->getLoaders() as $loader) {
            $loader->clearNamespace($namespace);
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
        $results = [];
        foreach ($this->getLoaders() as $loader) {
            $result = $loader->load($environment, $group, $namespace);;
            if ($result) {
                $results[] = $result;
            }
        }

        if ($results) {
            /** @TODO Replace with argument unpacking when we can support it */
            return call_user_func_array('array_replace_recursive', $results);
        }

        return [];
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
        foreach ($this->getLoaders() as $loader) {
            if ($loader->exists($group, $namespace)) {
                return true;
            }
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
        foreach ($this->getLoaders() as $loader) {
            $loader->addNamespace($namespace, $hint);
        }
    }

    /**
     * Returns all registered namespaces with the config
     * loader.
     *
     * @return array
     */
    public function getNamespaces()
    {
        $results = [];
        foreach ($this->getLoaders() as $loader) {
            $results = array_merge($results, $loader->getNamespaces());
        }

        return $results;
    }

    /**
     * Apply any cascades to an array of package options.
     *
     * @param string $environment
     * @param string $package
     * @param string $group
     * @param array $items
     *
     * @return array
     */
    public function cascadePackage($environment, $package, $group, $items)
    {
        $results = [];
        foreach ($this->getLoaders() as $loader) {
            $results = array_merge($results, $loader->cascadePackage($environment, $package, $group, $items));
        }

        return $results;
    }

    /**
     * Get the loaders associated with this class, populate instances using the application as needed
     *
     * @return array|LoaderInterface[]
     */
    private function getLoaders()
    {
        if (!$this->processed) {
            foreach ($this->loaders as &$loader) {
                if (is_string($loader)) {
                    $loader = $this->app->make($loader);
                }
            }
            $this->processed = true;
        }

        return $this->loaders;
    }
}
