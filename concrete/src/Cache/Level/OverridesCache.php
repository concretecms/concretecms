<?php
namespace Concrete\Core\Cache\Level;

use Concrete\Core\Cache\Cache;
use Config;
use Stash\Driver\BlackHole;
use Stash\Exception\InvalidArgumentException;
use Stash\Pool;

/**
 * Class ExpensiveCache
 * This cache stores data about where files are located in concrete5.
 *
 * \@package Concrete\Core\Cache\Level
 */
class OverridesCache extends Cache
{
    protected function init()
    {
        try {
            if (Config::get('concrete.cache.overrides') == true) {
                $driver = $this->loadConfig('overrides');
                $this->pool = new Pool($driver);
            } else {
                $this->pool = new Pool(new BlackHole());
            }
        } catch(InvalidArgumentException $e) {
            $this->pool = new Pool(new BlackHole());
        }
        $this->enable();
    }
}
