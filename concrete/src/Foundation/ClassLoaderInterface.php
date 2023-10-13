<?php

namespace Concrete\Core\Foundation;

/**
 * @deprecated
 *
 * @see \Concrete\Core\Foundation\ClassAutoloader
 */
interface ClassLoaderInterface
{
    function register();
    function unregister();
}