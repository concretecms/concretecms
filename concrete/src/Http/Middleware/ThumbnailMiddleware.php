<?php
namespace Concrete\Core\Http\Middleware;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Database\Driver\PDOStatement as ConcretePDOStatement;
use Concrete\Core\Entity\File\File;
use Concrete\Core\File\Image\BasicThumbnailer;
use Concrete\Core\File\Image\Thumbnail\Type\Version;
use Concrete\Core\File\StorageLocation\StorageLocationInterface;
use Concrete\Core\Http\ResponseFactoryInterface;
use Doctrine\DBAL\Exception\InvalidFieldNameException;
use Doctrine\ORM\EntityManagerInterface;
use InvalidArgumentException;
use League\Flysystem\Filesystem;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;

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

        if ($this->app->isInstalled()) {
            $responseStatusCode = (int) $response->getStatusCode();
            if ($responseStatusCode === 200 || $responseStatusCode === 404) {
                $database = $this->tryGetConnection();
                if ($database !== null) {
                    try {
                        if ($responseStatusCode === 404) {
                            $searchThumbnailPath = $request->getRequestUri();
                        } else {
                            $searchThumbnailPath = null;
                        }
                        $paths = $this->getThumbnailPathsToGenerate($database, $searchThumbnailPath);
                        if ($paths !== null) {
                            if ($this->generateThumbnails($paths, $database, $searchThumbnailPath) === true) {
                                $response = $this->buildRedirectToThumbnailResponse($request);
                                $searchThumbnailPath = null;
                            }
                            $paths->closeCursor();
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
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function buildRedirectToThumbnailResponse(Request $request)
    {
        $redirectTo = (string) $request->getUri();
        $redirectTo .= ((strpos($redirectTo, '?') === false) ? '?' : '&') . mt_rand();
        $responseFactory = $this->app->make(ResponseFactoryInterface::class);
        $response = $responseFactory->redirect($redirectTo);

        return $response;
    }

    /**
     * @param Connection $database
     * @param string|null $searchThumbnailPath
     *
     * @return ConcretePDOStatement|null
     */
    private function getThumbnailPathsToGenerate(Connection $database, $searchThumbnailPath = null)
    {
        if ($searchThumbnailPath === null) {
            $rs = $database->executeQuery('SELECT * FROM FileImageThumbnailPaths WHERE isBuilt = 0 LIMIT 5');
        } else {
            $rs = $database->executeQuery('SELECT * FROM FileImageThumbnailPaths WHERE isBuilt = 0 ORDER BY ' . $database->getDatabasePlatform()->getLocateExpression('?', 'path') . ' DESC LIMIT 5', [$searchThumbnailPath]);
        }
        if (!$rs->rowCount()) {
            $rs->closeCursor();
            $rs = null;
        }

        return $rs;
    }

    /**
     * Generate thumbnails and and manage db update.
     *
     * @param ConcretePDOStatement $paths
     * @param \Concrete\Core\Database\Connection\Connection $database
     * @param string|null $searchThumbnailPath
     *
     * @return bool returns true if $searchThumbnailPath is set and one of the thumbnails may be for that thumbnail
     */
    private function generateThumbnails(ConcretePDOStatement $paths, Connection $database, $searchThumbnailPath)
    {
        $result = false;
        $database->transactional(function (Connection $database) use ($paths, $searchThumbnailPath, &$result) {
            foreach ($paths as $thumbnail) {
                /** @var File $file */
                $file = $this->getEntityManager()->find(File::class, $thumbnail['fileID']);

                if ($this->attemptBuild($file, $thumbnail)) {
                    $this->completeBuild($file, $thumbnail);
                    if ($result === false && $this->maybeTheRequestedThumbnail($thumbnail, $searchThumbnailPath)) {
                        $result = true;
                    }
                } else {
                    $this->failBuild($file, $thumbnail);
                }
            }
        });

        return $result;
    }

    /**
     * Try building an unbuild thumbnail.
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

                $this->getThumbnailer()->getThumbnail($file, $width, $height, (bool) $crop);
            } elseif ($type = Version::getByHandle($thumbnail['thumbnailTypeHandle'])) {
                // This is a predefined thumbnail type, lets just call the version->rescan
                $file->getVersion($thumbnail['fileVersionID'])->generateThumbnail($type);
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
     * @param array $thumbnail
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
     * @return Connection|null
     */
    private function tryGetConnection()
    {
        try {
            return $this->getConnection();
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }

    /**
     * Mark the build complete.
     *
     * @param $file
     * @param $thumbnail
     */
    private function completeBuild(File $file, $thumbnail)
    {
        $key = $thumbnail;
        unset($key['path']);
        unset($key['isBuilt']);
        // Update the database to have "1" for isBuilt
        $this->connection->update('FileImageThumbnailPaths', ['isBuilt' => '1'], $key);
    }

    /**
     * Check if a thumbnail array may be for the requested path.
     *
     * @param array $thumbnail
     * @param string|null $searchThumbnailPath
     *
     * @return bool
     */
    private function maybeTheRequestedThumbnail(array $thumbnail, $searchThumbnailPath)
    {
        $result = false;
        if ($searchThumbnailPath && substr($searchThumbnailPath, -strlen($thumbnail['path'])) === $thumbnail['path']) {
            $result = true;
        }

        return $result;
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
