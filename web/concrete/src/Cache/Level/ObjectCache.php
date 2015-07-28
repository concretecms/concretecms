<?php
namespace Concrete\Core\Cache\Level;

use Stash\Pool;

class ObjectCache extends \Cache
{
    protected function init()
    {
        $driver = $this->loadConfig('object');
        $this->pool = new Pool($driver);
        $this->enable();
    }
} 