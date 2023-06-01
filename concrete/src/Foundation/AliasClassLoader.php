<?php

namespace Concrete\Core\Foundation;

/**
 * @deprecated
 * @see \Concrete\Core\Foundation\ClassAutoloader
 */
class AliasClassLoader
{
    private $classAliasList;

    public function __construct(ClassAliasList $classAliasList)
    {
        $this->classAliasList = $classAliasList;
    }

    public function register($prepend = false)
    {
        ClassAutoloader::getInstance()->addClassAliases($this->classAliasList->getRegisteredAliases());
    }

    public function loadClass($class)
    {
        ClassAutoloader::getInstance()->loadClass($class);
    }
}
