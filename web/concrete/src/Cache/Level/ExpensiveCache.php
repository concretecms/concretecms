<?php

namespace Concrete\Core\Cache\Level;


use Concrete\Core\Cache\Cache;
use Stash\Pool;

/**
 * Class ExpensiveCache
 * This cache stores data that is expensive to build that will see a performance boost if stored on disk.
 * @package Concrete\Core\Cache\Level
 */
class ExpensiveCache extends Cache
{
    protected function init()
    {
        $driver = $this->loadConfig('expensive');
        $this->pool = new Pool($driver);
        $this->enable();
    }
} 