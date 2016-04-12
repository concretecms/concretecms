<?php
namespace Concrete\Core\File\Image\Thumbnail\Path;

use Concrete\Core\Application\Application;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\File\File;
use Concrete\Core\File\Image\Thumbnail\Type\Type;
use Concrete\Core\File\Image\Thumbnail\Type\Version as ThumbnailVersion;
use Concrete\Core\File\Version;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;

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
        $version_id = $file_version->getFileVersionID();
        $storage_location_id = $file->getStorageLocationID();
        $thumbnail_handle = $thumbnail->getHandle();

        $path = $this->getStoredPath(
            $file_id,
            $version_id,
            $storage_location_id,
            $thumbnail_handle);

        if ($path) {
            return $path;
        } elseif ($path = $this->determinePath($file_version, $thumbnail)) {
            $this->storePath(
                $path,
                $file->getFileID(),
                $file_version->getFileVersionID(),
                $storage_location_id,
                $thumbnail_handle);

            return $path;
        }
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
    protected function storePath($path, $file_id, $version_id, $storage_location_id, $thumbnail_handle)
    {
        $this->connection->insert('FileImageThumbnailPaths', array(
            'path' => $path,
            'fileID' => $file_id,
            'fileVersionID' => $version_id,
            'storageLocationID' => $storage_location_id,
            'thumbnailTypeHandle' => $thumbnail_handle
        ));
    }

    /**
     * Determine the path for a file version thumbnail based on the configured storage location
     *
     * @param \Concrete\Core\File\Version $file_version
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version $thumbnail
     * @return string
     */
    protected function determinePath(Version $file_version, ThumbnailVersion $thumbnail)
    {
        $file = $file_version->getFile();
        $storage_location = $file->getFileStorageLocationObject();

        if ($storage_location) {
            $configuration = $storage_location->getConfigurationObject();
            $fss = $storage_location->getFileSystemObject();
            $path = $thumbnail->getFilePath($file_version);

            if ($fss->has($path)) {
                return $configuration->getPublicURLToFile($path);
            }
        }

        return $this->getDefaultPath($file_version, $thumbnail);
    }

    /**
     * Fallback to getting the
     *
     * @param \Concrete\Core\File\Version $file_version
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version $thumbnail
     * @return string
     */
    protected function getDefaultPath(Version $file_version, ThumbnailVersion $thumbnail)
    {
        $cf = $this->app->make('helper/concrete/file');

        $fsl = $file_version->getFile()->getFileStorageLocationObject();

        if (is_object($fsl)) {
            $configuration = $fsl->getConfigurationObject();

            if ($configuration->hasPublicURL()) {
                $file = $cf->prefix($file_version->getPrefix(), $file_version->getFileName());
                return $configuration->getPublicURLToFile($file);
            }
        }
    }

}
