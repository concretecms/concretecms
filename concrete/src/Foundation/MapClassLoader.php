<?php

namespace Concrete\Core\Foundation;

use Symfony\Component\ClassLoader\MapClassLoader as SymfonyMapClassLoader;

/**
 * @deprecated
 *
 * @see \Concrete\Core\Foundation\ClassAutoloader
 */
class MapClassLoader extends SymfonyMapClassLoader implements ClassLoaderInterface
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Foundation\ClassLoaderInterface::unregister()
     */
    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }
}
