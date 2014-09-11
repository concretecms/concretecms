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
            new Ephemeral()
        );
        $fsDriver = new FileSystem();
        $fsDriver->setOptions(array('path' => DIR_FILES_CACHE));
        $drivers[] = $fsDriver;

        $driver = new Composite();
        $driver->setOptions(array('drivers' => $drivers));
        $this->pool = new Pool($driver);
        $this->enable();
    }
} 