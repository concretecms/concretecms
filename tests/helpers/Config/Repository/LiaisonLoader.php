<?php

namespace Concrete\TestHelpers\Config\Repository;

use Concrete\Core\Config\LoaderInterface;

class LiaisonLoader implements LoaderInterface
{
    public function addNamespace($namespace, $hint)
    {
    }

    public function cascadePackage($environment, $package, $group, $items)
    {
        return $items;
    }

    public function clearNamespace($namespace)
    {
    }

    public function load($environment, $group, $namespace = null)
    {
        return [];
    }

    public function exists($group, $namespace = null)
    {
        return true;
    }

    public function getNamespaces()
    {
        return [];
    }
}
