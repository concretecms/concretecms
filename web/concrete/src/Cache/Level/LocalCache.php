<?php

namespace Concrete\Core\Cache\Level;


use Concrete\Core\Cache\Cache;
use Stash\Driver\Ephemeral;
use Stash\Pool;

class LocalCache extends Cache
{
    protected function init()
    {
        $this->pool = new Pool(new Ephemeral());
        $this->enable();
    }
}