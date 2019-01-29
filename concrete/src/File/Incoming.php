<?php

namespace Concrete\Core\File;

use Concrete\Core\File\StorageLocation\StorageLocationFactory;

/**
 * A seervice class to manage data in the "incoming" folder.
 */
class Incoming
{
    /**
     * The StorageLocation factory.
     *
     * @var \Concrete\Core\File\StorageLocation\StorageLocationFactory
     */
    protected $storageLocationFactory;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\File\StorageLocation\StorageLocationFactory $storageLocationFactory
     */
    public function __construct(StorageLocationFactory $storageLocationFactory)
    {
        $this->storageLocationFactory = $storageLocationFactory;
    }

    /**
     * Get the path of the "incoming" directory (relative to the incoming filesystem).
     *
     * @return string
     */
    public function getIncomingPath()
    {
        return rtrim(REL_DIR_FILES_INCOMING, '/');
    }

    /**
     * Get the storage location for the "incoming" files.
     *
     * @return \Concrete\Core\Entity\File\StorageLocation\StorageLocation
     */
    public function getIncomingStorageLocation()
    {
        return $this->storageLocationFactory->fetchDefault();
    }

    /**
     * Get the filesystem object to be used to work with the incoming directory.
     *
     * @return \League\Flysystem\FilesystemInterface
     */
    public function getIncomingFilesystem()
    {
        return $this->getIncomingStorageLocation()->getFileSystemObject();
    }
}
