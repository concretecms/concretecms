<?php
namespace Concrete\Core\File\Image\Thumbnail\Path;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\File\File;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\Image\Thumbnail\Type\Version as ThumbnailVersion;
use Concrete\Core\File\StorageLocation\Configuration\ConfigurationInterface;
use Concrete\Core\File\StorageLocation\Configuration\DeferredConfigurationInterface;
use Doctrine\DBAL\Exception\InvalidFieldNameException;

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
        $realPath = null;

        $path = $this->getStoredPath(
            $file_id,
            $version_id,
            $storage_location_id,
            $thumbnail_handle);

        if (!$path) {
            if ($path = $this->determinePath($file_version, $thumbnail, $storage_location, $configuration)) {
                $realPath = $this->getBuiltPath($path, $file_version, $thumbnail, $storage_location, $configuration);
                $this->storePath($path, $file_id, $version_id, $storage_location_id, $thumbnail_handle, $realPath == $path);
            }
        }

        if (!$realPath) {
            $realPath = $this->getBuiltPath($path, $file_version, $thumbnail, $storage_location, $configuration);
        }

        if ($path == $realPath) {
            if ($configuration instanceof DeferredConfigurationInterface) {
                return $configuration->getPublicURLToFile($path);
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
    }

    /**
     * Store a path in the database against a storage location for a file version and a thumbnail handle
     *
     * @param $path
     * @param $file_id
     * @param $version_id
     * @param $storage_location_id
     * @param $thumbnail_handle
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
                'isBuilt' => $isBuilt
            ));
        } catch (InvalidFieldNameException $e) {
            // User needs to run the database upgrade routine
        }
    }

    /**
     * Determine the path for a file version thumbnail based on the storage location
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
        \Concrete\Core\Entity\File\StorageLocation\StorageLocation $storage,
        ConfigurationInterface $configuration
    ) {
        $path = $thumbnail->getFilePath($file_version);

        if ($configuration instanceof DeferredConfigurationInterface) {
            return $path;
        }

        return $configuration->getPublicURLToFile($path);
    }

    protected function getBuiltPath(
        $path,
        Version $file_version,
        ThumbnailVersion $thumbnail,
        \Concrete\Core\Entity\File\StorageLocation\StorageLocation $storage,
        ConfigurationInterface $configuration
    ) {
        return $path;
    }

    /**
     * Get the main image path ignoring the thumbnail requirements
     *
     * @param \Concrete\Core\Entity\File\Version $file_version
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version $thumbnail
     * @return string
     */
    protected function getDefaultPath(
        Version $file_version,
        ThumbnailVersion $thumbnail,
        \Concrete\Core\Entity\File\StorageLocation\StorageLocation $storage,
        ConfigurationInterface $configuration
    ) {
        $cf = $this->app->make('helper/concrete/file');

        if ($configuration->hasPublicURL()) {
            $file = $cf->prefix($file_version->getPrefix(), $file_version->getFileName());

            if ($configuration instanceof DeferredConfigurationInterface) {
                return $file;
            }

            return $configuration->getPublicURLToFile($file);
        }

    }

}
