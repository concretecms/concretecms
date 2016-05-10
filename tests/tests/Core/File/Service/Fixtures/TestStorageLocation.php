<?php

namespace Concrete\Tests\Core\File\Service\Fixtures;
use Concrete\Core\File\StorageLocation\StorageLocation;
use League\Flysystem\Cached\Storage\Noop;

class TestStorageLocation extends StorageLocation
{
    /**
     * Returns the proper file system object for the current storage location, by mapping
     * it through Flysystem
     * @return \League\Flysystem\Filesystem
     */
    public function getFileSystemObject()
    {
        $adapter = $this->getConfigurationObject()->getAdapter();
        $filesystem = new \League\Flysystem\Filesystem($adapter, new Noop());
        return $filesystem;
    }
}