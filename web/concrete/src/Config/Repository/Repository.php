<?php
namespace Concrete\Core\Config\Repository;

use Concrete\Core\Config\LoaderInterface;
use Concrete\Core\Config\SaverInterface;

class Repository extends \Illuminate\Config\Repository
{

    /**
     * @var SaverInterface
     */
    protected $saver;

    /**
     * @var LoaderInterface
     */
    protected $loader;

    /**
     * Create a new configuration repository.
     *
     * @param LoaderInterface $loader
     * @param SaverInterface  $saver
     * @param                 $environment
     */
    public function __construct(LoaderInterface $loader, SaverInterface $saver, $environment)
    {
        $this->saver = $saver;
        parent::__construct($loader, $environment);
    }

    /**
     * Clear specific key
     *
     * @param string $key
     */
    public function clear($key)
    {
        $this->set($key, null);
    }

    /**
     * Save a key
     *
     * @param $key
     * @param $value
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
     * Register a package for cascading configuration.
     *
     * @param  string $package
     * @param  string $hint
     * @param  string $namespace
     * @return void
     */
    public function package($package, $hint, $namespace = null)
    {
        $namespace = $this->getPackageNamespace($package, $namespace);

        $this->packages[] = $namespace;
        $this->addNamespace($namespace, $hint);
    }

    protected function getPackageNamespace($package, $namespace)
    {
        return $namespace ?: $package;
    }

    public function clearCache()
    {
        $this->items = array();
    }

    public function clearNamespace($namespace) {
        $this->loader->clearNamespace($namespace);
    }

    /**
     * @return SaverInterface
     */
    public function getSaver()
    {
        return $this->saver;
    }

    protected function parsePackageSegments($key, $namespace, $item)
    {
        list($namespace, $item) = explode('::', $key);

        // First we'll just explode the first segment to get the namespace and group
        // since the item should be in the remaining segments. Once we have these
        // two pieces of data we can proceed with parsing out the item's value.
        $itemSegments = explode('.', $item);

        $groupAndItem = array_slice($this->parseBasicSegments($itemSegments), 1);

        return array_merge(array($namespace), $groupAndItem);
    }

}
