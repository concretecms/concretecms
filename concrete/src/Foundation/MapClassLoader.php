<?php

namespace Concrete\Core\Foundation;

use Symfony\Component\ClassLoader\MapClassLoader as SymfonyMapClassLoader;

class MapClassLoader extends SymfonyMapClassLoader implements ClassLoaderInterface
{

    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }


}
