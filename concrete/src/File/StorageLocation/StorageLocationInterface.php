<?php
namespace Concrete\Core\File\StorageLocation;

/**
 * @Entity
 * @Table(name="FileStorageLocations")
 */
interface StorageLocationInterface
{

    /**
     * Get the configuration for this storage location
     * @return \Concrete\Core\File\StorageLocation\Configuration\ConfigurationInterface
     */
    public function getConfigurationObject();

    /**
     * Is this storage location default
     * @return bool
     */
    public function isDefault();

    /**
     * Returns the proper file system object for the current storage location by mapping
     * it through Flysystem.
     *
     * @return \League\Flysystem\Filesystem
     */
    public function getFileSystemObject();

    /**
     * Delete this storage location
     *
     * @return mixed
     */
    public function delete();

    /**
     * Save a storage location
     *
     * @return mixed
     */
    public function save();

}
