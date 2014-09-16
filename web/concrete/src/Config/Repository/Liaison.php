<?php
namespace Concrete\Core\Config\Repository;

class Liaison
{

    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    protected $repository;

    /**
     * @var string Default Namespace
     */
    protected $default_namespace;

    /**
     * Create a new configuration repository.
     *
     * @param \Concrete\Core\Config\Repository\Repository $repository
     * @param string                                      $default_namespace
     */
    public function __construct(\Concrete\Core\Config\Repository\Repository $repository, $default_namespace)
    {
        $this->default_namespace = $default_namespace;
        $this->repository = $repository;
    }

    protected function transformKey($key)
    {
        list($namespace, $group, $item) = $this->repository->parseKey($key);
        if (!$namespace) {
            $namespace = $this->default_namespace;
        }

        $collection = "{$namespace}::{$group}";
        if ($item) {
            $collection .= ".{$item}";
        }

        return $collection;
    }

    public function has($key)
    {
        return $this->repository->has($this->transformKey($key));
    }

    public function get($key, $default = null)
    {
        return $this->repository->get($this->transformKey($key), $default);
    }

    public function save($key, $value)
    {
        return $this->repository->save($this->transformKey($key), $value);
    }

    public function set($key, $value)
    {
        $this->repository->set($this->transformKey($key), $value);
    }

    public function clear($key)
    {
        $this->repository->clear($this->transformKey($key));
    }

}
