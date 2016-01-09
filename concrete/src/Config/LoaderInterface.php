<?php
namespace Concrete\Core\Config;

interface LoaderInterface extends \Illuminate\Config\LoaderInterface
{

    /**
     * @param $namespace
     * @return void
     */
    public function clearNamespace($namespace);

}
