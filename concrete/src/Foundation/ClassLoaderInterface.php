<?php

namespace Concrete\Core\Foundation;

/**
 * @since 8.0.0
 */
interface ClassLoaderInterface
{
    function register();
    function unregister();
}