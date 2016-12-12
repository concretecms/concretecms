<?php

namespace Concrete\Core\Cache\Level;


use Concrete\Core\Cache\Cache;
use Config;
use Stash\Driver\BlackHole;
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
        if (Config::get('concrete.cache.enabled') == true) {
            $driver = $this->loadConfig('expensive');
            $this->pool = new Pool($driver);
        } else {
            $this->pool = new Pool(new BlackHole());
        }
        $this->enable();
    }
} 