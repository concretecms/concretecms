<?php
namespace Concrete\Core\Config;

use Illuminate\Config\LoaderInterface as IlluminateLoaderInterface;

interface LoaderInterface extends IlluminateLoaderInterface
{
    /**
     * @param $namespace
     */
    public function clearNamespace($namespace);
}
