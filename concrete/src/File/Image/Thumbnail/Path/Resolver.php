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
use Concrete\Core\File\Image\Thumbnail\ThumbnailFormatService;

class Resolver
{

    /**
     * @var \Concrete\Core\Application\Application
     */
    protected $app;

    /**
     * @var \Concrete\Core\Database\Connection\Connection
     */
    protected $connection;

    /**
     * @var \Concrete\Core\File\Image\Thumbnail\ThumbnailFormatService
     */
    protected $formatService;

    /**
     * Resolver constructor.
     * @param \Concrete\Core\Application\Application $app
     * @param \Concrete\Core\Database\Connection\Connection $connection
     */
    public function __construct(Application $app, Connection $connection, ThumbnailFormatService $formatService)
    {
        $this->app = $app;
        $this->connection = $connection;
        $this->formatService = $formatService;
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
        $format = $this->formatService->getFormatForFile($file_version);
        $file_id = $file->getFileID();
        $storage_location = $file->getFileStorageLocationObject();
        $configuration = $storage_location->getConfigurationObject();
        $version_id = $file_version->getFileVersionID();
        $storage_location_id = $storage_location->getID();
        $thumbnail_handle = $thumbnail->getHandle();
        $defer = $configuration instanceof DeferredConfigurationInterface;

        // Get the path from the storage
        $path = $this->getStoredThumnbailPath(
            $file_id,
            $version_id,
            $storage_location_id,
            $thumbnail_handle,
            $format
        );

        // If we don't have a stored path already, lets determine one and store it
        if (!$path) {
            $path = $this->determineThumbnailPath($file_version, $thumbnail, $storage_location, $configuration, $format);
            if ($path) {
                $this->storeThumbnailPath($path, $file_id, $version_id, $storage_location_id, $thumbnail_handle, $format, !$defer);
            }
        }

        // Pass the path to the "getBuiltThumbnailPath" method which will alter the path if it wants to
        $realPath = $this->getBuiltThumbnailPath($path, $file_version, $thumbnail, $storage_location, $configuration, $format);

        // If the "getBuiltThumbnailPath" method didn't alter the path, lLet's let the configuration resolve the path now
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
     * Get the stored path for a file.
     *
     * @param int $file_id
     * @param int $version_id
     * @param int $storage_location_id
     * @param string $thumbnail_handle
     * @param string $format
     *
     * @return null|string
     */
    protected function getStoredThumnbailPath($file_id, $version_id, $storage_location_id, $thumbnail_handle, $format)
    {
        $builder = $this->connection->createQueryBuilder();
        $query = $builder
            ->select('path')->from('FileImageThumbnailPaths', 'p')
            ->where('p.fileID = :file')
            ->andWhere('p.fileVersionID = :version')
            ->andWhere('p.storageLocationID = :storage')
            ->andWhere('p.thumbnailTypeHandle = :thumbnail')
            ->andWhere('p.thumbnailFormat = :format')
            ->setParameters(array(
                'file' => $file_id,
                'version' => $version_id,
                'storage' => $storage_location_id,
                'thumbnail' => $thumbnail_handle,
                'format' => $format,
            ))->execute();

        if ($query->rowCount()) {
            return $query->fetchColumn();
        }

        return null;
    }

    /**
     * Store a path against a storage location for a file version and a thumbnail handle and format
     * @param string $path
     * @param int $file_id
     * @param int $version_id
     * @param int $storage_location_id
     * @param string $thumbnail_handle
     * @param string $format
     * @param bool $isBuilt Have we had the configuration generate the path yet
     */
    protected function storeThumbnailPath($path, $file_id, $version_id, $storage_location_id, $thumbnail_handle, $format, $isBuilt = true)
    {
        try {
            $this->connection->insert('FileImageThumbnailPaths', array(
                'path' => $path,
                'fileID' => $file_id,
                'fileVersionID' => $version_id,
                'storageLocationID' => $storage_location_id,
                'thumbnailTypeHandle' => $thumbnail_handle,
                'thumbnailFormat' => $format,
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
     * to override would be `->getBuiltThumbnailPath()`
     *
     * @param \Concrete\Core\Entity\File\Version $file_version
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version $thumbnail
     * @param \Concrete\Core\Entity\File\StorageLocation\StorageLocation $storage
     * @param \Concrete\Core\File\StorageLocation\Configuration\ConfigurationInterface $configuration
     * @param string $format
     * @return string|null
     */
    protected function determineThumbnailPath(Version $file_version, ThumbnailVersion $thumbnail, StorageLocation $storage, ConfigurationInterface $configuration, $format)
    {
        if ($thumbnail->shouldExistFor($file_version->getAttribute('width'), $file_version->getAttribute('height'), $file_version->getFile())) {
            $path = $thumbnail->getFilePath($file_version, $format);
    
            if ($configuration instanceof DeferredConfigurationInterface) {
                // Lets defer getting the path from the configuration until we know we need to
                return $path;
            }
    
            return $configuration->getRelativePathToFile($path);
        }
    }

    /**
     * An access point for overriding how paths are built
     * @param $path
     * @param \Concrete\Core\Entity\File\Version $file_version
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version $thumbnail
     * @param \Concrete\Core\Entity\File\StorageLocation\StorageLocation $storage
     * @param \Concrete\Core\File\StorageLocation\Configuration\ConfigurationInterface $configuration
     * @param string $format
     * @return mixed
     */
    protected function getBuiltThumbnailPath($path, Version $file_version, ThumbnailVersion $thumbnail, StorageLocation $storage, ConfigurationInterface $configuration, $format)
    {
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

    /**
     * @deprecated Use getStoredThumnbailPath
     */
    protected function getStoredPath($file_id, $version_id, $storage_location_id, $thumbnail_handle)
    {
        $versionObject = $this->connection->getEntityManager()->find(Version::class, ['fID' => $file_id, 'fvID' => $version_id]);
        
        return $this->getStoredThumnbailPath($file_id, $version_id, $storage_location_id, $thumbnail_handle, $this->formatService->getFormatForFile($versionObject));
    }

    /**
     * @deprecated Use storeThumbnailPath
     */
    protected function storePath($path, $file_id, $version_id, $storage_location_id, $thumbnail_handle, $isBuilt = true)
    {
        $versionObject = $this->connection->getEntityManager()->find(Version::class, ['fID' => $file_id, 'fvID' => $version_id]);
        
        return $this->storeThumbnailPath($path, $file_id, $version_id, $storage_location_id, $thumbnail_handle, $this->formatService->getFormatForFile($versionObject), $isBuilt);
    }

    /**
     * @deprecated Use determineThumbnailPath
     */
    protected function determinePath(Version $file_version, ThumbnailVersion $thumbnail, StorageLocation $storage, ConfigurationInterface $configuration)
    {
        return $this->determineThumbnailPath($file_version, $thumbnail, $storage, $configuration, $this->formatService->getFormatForFile($file_version));
    }

    /**
     * @deprecated Use getBuiltThumbnailPath
     */
    protected function getBuiltPath($path, Version $file_version, ThumbnailVersion $thumbnail, StorageLocation $storage, ConfigurationInterface $configuration)
    {
        return $this->getBuiltThumbnailPath($path, $file_version, $thumbnail, $storage, $configuration, $this->formatService->getFormatForFile($file_version));
    }
}
