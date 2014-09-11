<?php
namespace Concrete\Core\Config;

class Repository extends \Illuminate\Config\Repository
{

    /**
     * @var SaverInterface
     */
    protected $saver;

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
     * Get a value, if it's not set, save the passed value
     *
     * @param string $key
     * @param mixed  $value
     * @return mixed
     */
    public function getOrSet($key, $value)
    {
        $val = $this->get($key, $this);

        if ($val === $this) {
            $this->save($key, $value);
            return $value;
        }

        return $val;
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

}
