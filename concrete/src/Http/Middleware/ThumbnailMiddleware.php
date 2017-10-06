<?php
namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\File\File;
use Concrete\Core\File\Image\BasicThumbnailer;
use Concrete\Core\File\Image\Thumbnail\Type\CustomThumbnail;
use Concrete\Core\File\Image\Thumbnail\Type\Version;
use Concrete\Core\File\StorageLocation\StorageLocationInterface;
use Doctrine\DBAL\Exception\InvalidFieldNameException;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\Filesystem;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Concrete\Core\Config\Repository\Repository;
/**
 * Class ThumbnailMiddleware
 * Middleware used to populate thumbnails at the end of each request.
 *
 * This middleware requires the following to be defined on the Application:
 * "database" DatabaseManager
 * "BasicThumbnailer::class" Basic thumbnailer
 */
class ThumbnailMiddleware implements MiddlewareInterface, ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    /**
     * @var Filesystem
     */
    protected $baseFileSystem;

    /**
     * @var \Concrete\Core\File\Image\BasicThumbnailer
     */
    private $thumbnailer;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $entityManager;

    /**
     * @var Connection
     */
    private $connection;


    /**
     * @var \Concrete\Core\Config\Repository\Repository
     */
    private $config;

    public function __construct(Repository $config)
    {
        $this->config = $config;
    }

    /**
     * Process the request and return a response.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param DelegateInterface                         $frame
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function process(Request $request, DelegateInterface $frame)
    {
        $response = $frame->next($request);

        // if the thumbnail generator is async, we do not use the thumbnail middleware.

        if ($this->app->isInstalled() && $this->config->get('concrete.misc.basic_thumbnailer_generation_strategy') == 'now') {
            if ($response->getStatusCode() == 200) {
                /* @var Connection $database */
                try {
                    $database = $this->getConnection();
                } catch (\InvalidArgumentException $e) {
                    // Don't die here if there's no available database connection
                    $database = null;
                }

                if ($database) {
                    try {
                        $paths = $database->executeQuery('SELECT * FROM FileImageThumbnailPaths WHERE isBuilt=0 LIMIT 5');
                        if ($paths->rowCount()) {
                            $this->generateThumbnails($paths, $database);
                        }
                    } catch (InvalidFieldNameException $e) {
                        // Ignore this, user probably needs to run an upgrade.
                    }
                }
            }
        }

        return $response;
    }

    /**
     * Generate thumbnails and and manage db update.
     *
     * @param array[]                                       $paths
     * @param \Concrete\Core\Database\Connection\Connection $database
     */
    private function generateThumbnails($paths, Connection $database)
    {
        $database->transactional(function (Connection $database) use ($paths) {
            foreach ($paths as $thumbnail) {
                /** @var File $file */
                $file = $this->getEntityManager()->find(File::class, $thumbnail['fileID']);

                if ($this->attemptBuild($file, $thumbnail)) {
                    $this->completeBuild($file, $thumbnail);
                } else {
                    $this->failBuild($file, $thumbnail);
                }
            }
        });
    }

    /**
     * Try building an unbuilt thumbnail.
     *
     * @param \Concrete\Core\Entity\File\File $file
     * @param array                           $thumbnail
     *
     * @return bool
     */
    private function attemptBuild(File $file, array $thumbnail)
    {
        try {
            // If the file is already built, return early
            if ($this->isBuilt($file, $thumbnail)) {
                return true;
            }

            // Otherwise lets attempt to build it
            if ($dimensions = $this->getDimensions($thumbnail)) {
                list($width, $height, $crop) = $dimensions;
                $type = new CustomThumbnail($width, $height, $thumbnail['path'], $crop);
                $fv = $file->getVersion($thumbnail['fileVersionID']);
                if ($fv->getTypeObject()->supportsThumbnails()) {
                    $fv->generateThumbnail($type);
                    $fv->releaseImagineImage();
                }
            } elseif ($type = Version::getByHandle($thumbnail['thumbnailTypeHandle'])) {
                // This is a predefined thumbnail type, lets just call the version->rescan
                $fv = $file->getVersion($thumbnail['fileVersionID']);

                if ($fv->getTypeObject()->supportsThumbnails()) {
                    $fv->generateThumbnail($type);
                    $fv->releaseImagineImage();
                }
            }
        } catch (\Exception $e) {
            // Catch any exceptions so we don't break the page and return false
            return false;
        }

        return $this->isBuilt($file, $thumbnail);
    }

    /**
     * Get the dimensions out of a thumbnail array.
     *
     * @param array $thumbnail
     *
     * @return array [ width, height, crop ]
     */
    private function getDimensions($thumbnail)
    {
        $matches = null;
        if (preg_match('/ccm_(\d+)x(\d+)(?:_([10]))?/', $thumbnail['thumbnailTypeHandle'], $matches)) {
            return array_pad(array_slice($matches, 1), 3, 0);
        }
    }

    /**
     * @param \Concrete\Core\Entity\File\File $file
     * @param array                           $thumbnail
     *
     * @return bool
     */
    private function isBuilt(File $file, $thumbnail)
    {
        $path = $thumbnail['path'];
        $location = $this->storageLocation();

        if ($path) {
            return
                // If the thumbnailer's storage location has the path
                ($location && $location->getFileSystemObject()->has($path)) ||
                // Or the file's storage location has the path
                $file->getFileStorageLocationObject()->getFileSystemObject()->has($path);
        }

        return false;
    }

    /**
     * @return null|StorageLocationInterface
     */
    private function storageLocation()
    {
        $thumbnailer = $this->getThumbnailer();
        if (method_exists($thumbnailer, 'getStorageLocation')) {
            $thumbnailerLocation = $thumbnailer->getStorageLocation();
            if ($thumbnailerLocation instanceof StorageLocationInterface) {
                return $thumbnailerLocation;
            }
        }

        return;
    }

    /**
     * @return \Concrete\Core\File\Image\BasicThumbnailer
     */
    protected function getThumbnailer()
    {
        if (!$this->thumbnailer) {
            $this->thumbnailer = $this->app->make(BasicThumbnailer::class);
        }

        return $this->thumbnailer;
    }

    /**
     * @return \Doctrine\ORM\EntityManagerInterface
     */
    protected function getEntityManager()
    {
        if (!$this->entityManager) {
            $this->entityManager = $this->app->make(EntityManagerInterface::class);
        }

        return $this->entityManager;
    }

    /**
     * @return Connection
     */
    protected function getConnection()
    {
        if (!$this->connection) {
            $this->connection = $this->app->make(Connection::class);
        }

        return $this->connection;
    }

    /**
     * Mark the build complete.
     *
     * @param $file
     * @param $thumbnail
     */
    private function completeBuild(File $file, $thumbnail)
    {
        unset($thumbnail['lockID']);
        unset($thumbnail['lockExpires']);
        // Update the database to have "1" for isBuilt
        $this->connection->update('FileImageThumbnailPaths', ['isBuilt' => '1'], $thumbnail);
    }

    /**
     * Mark the build failed.
     *
     * @param \Concrete\Core\Entity\File\File $file
     * @param $thumbnail
     */
    private function failBuild(File $file, $thumbnail)
    {
        $this->app->make(LoggerInterface::class)
            ->critical('Failed to generate or locate the thumbnail for file "' . $file->getFileID() . '"');

        // Complete the build anyway.
        // Cache must be cleared to remove this and attempt rebuild
        $this->completeBuild($file, $thumbnail);
    }
}
