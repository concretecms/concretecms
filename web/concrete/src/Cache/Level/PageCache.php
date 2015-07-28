<?php
namespace Concrete\Core\Cache\Level;

use Concrete\Core\Cache\Cache;
use Config;
use Stash\Driver\BlackHole;
use Stash\Pool;

/**
 * Class PageCache
 * This cache stores full page caches
 * @package Concrete\Core\Cache\Level
 */
class PageCache extends Cache
{
    protected function init()
    {
        if (Config::get('concrete.cache.pages') != false) {
            $driver = $this->loadConfig('page');
            $this->pool = new Pool($driver);
        } else {
            $this->pool = new Pool(new BlackHole());
        }
        $this->enable();
    }
} 