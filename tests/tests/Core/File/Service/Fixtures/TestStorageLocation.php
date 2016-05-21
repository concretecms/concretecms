<?php

namespace Concrete\Tests\Core\File\Service\Fixtures;

use Concrete\Core\Entity\File\StorageLocation\StorageLocation;

class TestStorageLocation extends StorageLocation
{
    /**
     * Returns the proper file system object for the current storage location, by mapping
     * it through Flysystem
     * @return \Concrete\Flysystem\Filesystem
     */
    public function getFileSystemObject()
    {
        $adapter = $this->getConfigurationObject()->getAdapter();
        $filesystem = new \League\Flysystem\Filesystem($adapter);
        return $filesystem;
    }

}
