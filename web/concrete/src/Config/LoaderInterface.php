<?php
namespace Concrete\Core\Config;

interface LoaderInterface extends \Illuminate\Config\LoaderInterface
{
    /**
     * @param $namespace
     */
    public function clearNamespace($namespace);
}
