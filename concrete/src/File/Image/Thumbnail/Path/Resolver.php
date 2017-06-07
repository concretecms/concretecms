<?php
namespace Concrete\Core\File\Image\Thumbnail\Path;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\StorageLocation\StorageLocation;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\Image\Thumbnail\Type\Version as ThumbnailVersion;
use Concrete\Core\File\StorageLocation\Configuration\ConfigurationInterface;
use Concrete\Core\File\StorageLocation\Configuration\DeferredConfigurationInterface;
use Doctrine\DBAL\Exception\InvalidFieldNameException;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

class Resolver
{

    protected $app;

    /**
     * @var \Concrete\Core\Database\Connection\Connection
     */
    protected $connection;

    /**
     * Resolver constructor.
     * @param \Concrete\Core\Application\Application $app
     * @param \Concrete\Core\Database\Connection\Connection $connection
     */
    public function __construct(Application $app, Connection $connection)
    {
        $this->app = $app;
        $this->connection = $connection;
    }

    /**
     * Get the path for a file version
     *
     * @param Version $file_version
     * @param ThumbnailVersion $thumbnail
     * @return null|string
     */
    public function getPath(Version $file_version, ThumbnailVersion $thumbnail)
    {
        /** @var File $file */
        $file = $file_version->getFile();
        $file_id = $file->getFileID();
        $storage_location = $file->getFileStorageLocationObject();
        $configuration = $storage_location->getConfigurationObject();
        $version_id = $file_version->getFileVersionID();
        $storage_location_id = $storage_location->getID();
        $thumbnail_handle = $thumbnail->getHandle();
        $defer = $configuration instanceof DeferredConfigurationInterface;

        // Get the path from the storage
        $path = $this->getStoredPath(
            $file_id,
            $version_id,
            $storage_location_id,
            $thumbnail_handle);

        // If we don't have a stored path already, lets determine one and store it
        if (!$path && $path = $this->determinePath($file_version, $thumbnail, $storage_location, $configuration)) {
            $this->storePath($path, $file_id, $version_id, $storage_location_id, $thumbnail_handle, !$defer);
        }

        // Pass the path to the "getBuiltPath" method which will alter the path if it wants to
        $realPath = $this->getBuiltPath($path, $file_version, $thumbnail, $storage_location, $configuration);

        // If the "getBuiltPath" method didn't alter the path, lLet's let the configuration resolve the path now
        if ($path == $realPath && $defer) {
            try {
                $realPath = $this->getPathFromConfiguration($realPath, $configuration);
            } catch (\InvalidArgumentException $e) {
                // Unable to resolve the path from the configuration object, lets return the real path
            }
        }

        return $realPath;
    }

    /**
     * Get the stored path for a file
     * @param int $file_id
     * @param int $version_id
     * @param int $storage_location_id
     * @param string $thumbnail_handle
     * @return null|string
     */
    protected function getStoredPath($file_id, $version_id, $storage_location_id, $thumbnail_handle)
    {
        $builder = $this->connection->createQueryBuilder();
        $query = $builder
            ->select('path')->from('FileImageThumbnailPaths', 'p')
            ->where('p.fileID = :file')
            ->andWhere('p.fileVersionID = :version')
            ->andWhere('p.storageLocationID = :storage')
            ->andWhere('p.thumbnailTypeHandle = :thumbnail')
            ->setParameters(array(
                'file' => $file_id,
                'version' => $version_id,
                'storage' => $storage_location_id,
                'thumbnail' => $thumbnail_handle
            ))->execute();

        if ($query->rowCount()) {
            return $query->fetchColumn();
        }

        return null;
    }

    /**
     * Store a path against a storage location for a file version and a thumbnail handle
     * @param $path
     * @param $file_id
     * @param $version_id
     * @param $storage_location_id
     * @param $thumbnail_handle
     * @param bool $isBuilt Have we had the configuration generate the path yet
     */
    protected function storePath($path, $file_id, $version_id, $storage_location_id, $thumbnail_handle, $isBuilt = true)
    {
        try {
            $this->connection->insert('FileImageThumbnailPaths', array(
                'path' => $path,
                'fileID' => $file_id,
                'fileVersionID' => $version_id,
                'storageLocationID' => $storage_location_id,
                'thumbnailTypeHandle' => $thumbnail_handle,
                'isBuilt' => $isBuilt ? 1 : 0
            ));
        } catch (InvalidFieldNameException $e) {
            // User needs to run the database upgrade routine
        } catch (UniqueConstraintViolationException $e) {
            // We tried to generate a thumbnail for something we already generated (race condition)
        }
    }

    /**
     * Determine the path for a file version thumbnail based on the storage location
     *
     * NOTE: If you're wanting to change how file paths are determined, a better method
     * to override would be `->getBuiltPath()`
     *
     * @param \Concrete\Core\Entity\File\Version $file_version
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version $thumbnail
     * @param \Concrete\Core\Entity\File\StorageLocation\StorageLocation $storage
     * @param \Concrete\Core\File\StorageLocation\Configuration\ConfigurationInterface $configuration
     * @return string
     */
    protected function determinePath(
        Version $file_version,
        ThumbnailVersion $thumbnail,
        StorageLocation $storage,
        ConfigurationInterface $configuration
    ) {
        $path = $thumbnail->getFilePath($file_version);

        if ($configuration instanceof DeferredConfigurationInterface) {
            // Lets defer getting the path from the configuration until we know we need to
            return $path;
        }

        return $configuration->getRelativePathToFile($path);
    }

    /**
     * An access point for overriding how paths are built
     * @param $path
     * @param \Concrete\Core\Entity\File\Version $file_version
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version $thumbnail
     * @param \Concrete\Core\Entity\File\StorageLocation\StorageLocation $storage
     * @param \Concrete\Core\File\StorageLocation\Configuration\ConfigurationInterface $configuration
     * @return mixed
     */
    protected function getBuiltPath(
        $path,
        Version $file_version,
        ThumbnailVersion $thumbnail,
        StorageLocation $storage,
        ConfigurationInterface $configuration
    ) {
        return $path;
    }

    /**
     * Get the path from a configuration object
     * @param string $path
     * @param \Concrete\Core\File\StorageLocation\Configuration\ConfigurationInterface $configuration
     * @return string
     */
    protected function getPathFromConfiguration($path, ConfigurationInterface $configuration)
    {
        if ($configuration->hasRelativePath()) {
            return $configuration->getRelativePathToFile($path);
        }

        if ($configuration->hasPublicURL()) {
            return $configuration->getPublicURLToFile($path);
        }

        throw new \InvalidArgumentException('Thumbnail configuration doesn\'t support Paths or URLs');
    }

}
