<?php

namespace Concrete\Core\Cache\Level;


use Concrete\Core\Cache\Cache;
use Stash\Driver\Composite;
use Stash\Driver\Ephemeral;
use Stash\Driver\FileSystem;
use Stash\Pool;

class ObjectCache extends Cache
{
    protected function init()
    {
        // todo move to config
        $drivers = array(
            new Ephemeral(),
            new FileSystem(array('path' => DIR_FILES_CACHE))
        );
        $this->pool = new Pool(new Composite(array('drivers' => $drivers)));
        $this->enable();
    }
} 