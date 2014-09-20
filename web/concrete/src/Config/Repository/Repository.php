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

    protected function getPackageNamespace($package, $namespace)
    {
        return $namespace ?: $package;
    }

}
