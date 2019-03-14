<?php

namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\File\File;
use Concrete\Core\File\Image\BasicThumbnailer;
use Concrete\Core\File\Image\Thumbnail\Type\CustomThumbnail;
use Concrete\Core\File\Image\Thumbnail\Type\Version;
use Concrete\Core\File\StorageLocation\StorageLocationInterface;
use Concrete\Core\Http\ResponseFactoryInterface;
use Doctrine\DBAL\Exception\InvalidFieldNameException;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use League\Flysystem\FileExistsException;
use League\Flysystem\Filesystem;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Throwable;

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
     * @param DelegateInterface $frame
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function process(Request $request, DelegateInterface $frame)
    {
        $response = $frame->next($request);

        if ($response && $this->app->isInstalled() && $this->config->get('concrete.misc.basic_thumbnailer_generation_strategy') == 'now') {
            $responseStatusCode = (int) $response->getStatusCode();
            if ($responseStatusCode === 200 || $responseStatusCode === 404) {
                $database = $this->tryGetConnection();
                if ($database !== null) {
                    if ($responseStatusCode === 404) {
                        $searchThumbnailPath = $request->getRequestUri();
                    } else {
                        $searchThumbnailPath = null;
                    }
                    $thumbnail = $this->getThumbnailToGenerate($database, $searchThumbnailPath);
                    if ($thumbnail !== null) {
                        $this->markThumbnailAsBuilt($database, $thumbnail);
                        if ($this->generateThumbnail($thumbnail)) {
                            if ($this->couldBeTheRequestedThumbnail($thumbnail, $searchThumbnailPath)) {
                                $response = $this->buildRedirectToThumbnailResponse($request);
                            }
                        }
                    }
                }
            }
        }

        return $response;
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
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function buildRedirectToThumbnailResponse(Request $request)
    {
        $redirectTo = (string) $request->getUri();
        $redirectTo .= ((strpos($redirectTo, '?') === false) ? '?' : '&') . mt_rand();
        $responseFactory = $this->app->make(ResponseFactoryInterface::class);
        $response = $responseFactory->redirect($redirectTo, 302);

        return $response;
    }

    /**
     * @param Connection $database
     * @param string|null $searchThumbnailPath
     *
     * @return array|null
     */
    private function getThumbnailToGenerate(Connection $database, $searchThumbnailPath = null)
    {
        try {
            if ($searchThumbnailPath === null) {
                $rs = $database->executeQuery('SELECT * FROM FileImageThumbnailPaths WHERE isBuilt = 0 LIMIT 1');
            } else {
                $rs = $database->executeQuery('SELECT * FROM FileImageThumbnailPaths WHERE isBuilt = 0 ORDER BY ' . $database->getDatabasePlatform()->getLocateExpression('?', 'path') . ' DESC LIMIT 1', [$searchThumbnailPath]);
            }
        } catch (InvalidFieldNameException $e) {
            // Ignore this, user probably needs to run an upgrade.
            return null;
        }

        return $rs->fetch() ?: null;
    }

    /**
     * Generate a thumbnail.
     *
     * @param array $thumbnail
     *
     * @return bool Returns true if success, false on failure
     */
    private function generateThumbnail(array $thumbnail)
    {
        $file = $this->getEntityManager()->find(File::class, $thumbnail['fileID']);

        if ($this->attemptBuild($file, $thumbnail)) {
            $result = true;
        } else {
            $this->failBuild($file, $thumbnail);
            $result = false;
        }

        return $result;
    }

    /**
     * Try building an unbuilt thumbnail.
     *
     * @param \Concrete\Core\Entity\File\File $file
     * @param array $thumbnail
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
        } catch (FileExistsException $e) {
            return true;
        } catch (Exception $e) {
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
    private function getDimensions(array $thumbnail)
    {
        $matches = null;
        if (preg_match('/ccm_(\d+)x(\d+)(?:_([10]))?/', $thumbnail['thumbnailTypeHandle'], $matches)) {
            return array_pad(array_slice($matches, 1), 3, 0);
        }
    }

    /**
     * @param \Concrete\Core\Entity\File\File $file
     * @param array $thumbnail
     *
     * @return bool
     */
    private function isBuilt(File $file, array $thumbnail)
    {
        $path = $thumbnail['path'];

        if ($path) {
            $location = $this->storageLocation();

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
    }

    /**
     * @return Connection|null
     */
    private function tryGetConnection()
    {
        try {
            $connection = $this->getConnection();
            $connection->connect();
        } catch (Exception $e) {
            return null;
        } catch (Throwable $e) {
            return null;
        }

        return $connection;
    }

    /**
     * Mark a thumbnail as built or not.
     *
     * @param Connection $connection
     * @param array $thumbnail
     * @param bool $built
     */
    private function markThumbnailAsBuilt(Connection $connection, array $thumbnail, $built = true)
    {
        $key = $thumbnail;
        unset($key['lockID']);
        unset($key['lockExpires']);
        unset($key['path']);
        unset($key['isBuilt']);
        $connection->update('FileImageThumbnailPaths', ['isBuilt' => $built ? 1 : 0], $key);
    }

    /**
     * Check if a thumbnail array may be for the requested path.
     *
     * @param array $thumbnail
     * @param string|null $searchThumbnailPath
     *
     * @return bool
     */
    private function couldBeTheRequestedThumbnail(array $thumbnail, $searchThumbnailPath)
    {
        return $searchThumbnailPath && substr($searchThumbnailPath, -strlen($thumbnail['path'])) === $thumbnail['path'];
    }

    /**
     * Mark the build failed.
     *
     * @param \Concrete\Core\Entity\File\File $file
     * @param array $thumbnail
     */
    private function failBuild(File $file, array $thumbnail)
    {
        $this->app->make(LoggerInterface::class)
            ->critical('Failed to generate or locate the thumbnail for file "' . $file->getFileID() . '"');
    }
}
