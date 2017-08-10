<?php

namespace Concrete\Controller\Backend\File;

use Concrete\Core\Controller\AbstractController;
use Concrete\Core\Entity\File\File;
use Concrete\Core\File\Image\BasicThumbnailer;
use Concrete\Core\File\Image\Thumbnail\AtomicThumbnailStream;
use Concrete\Core\File\Image\Thumbnail\Type\CustomThumbnail;
use Concrete\Core\File\Image\Thumbnail\Type\Version;
use Concrete\Core\File\StorageLocation\StorageLocationInterface;
use Concrete\Core\Http\ResponseFactory;
use Doctrine\ORM\EntityManagerInterface;

class Thumbnailer extends \Concrete\Core\Controller\Controller
{
    /** @var \Concrete\Core\Http\ResponseFactory */
    private $factory;

    /** @var \Concrete\Core\File\Image\Thumbnail\AtomicThumbnailStream */
    private $stream;

    /** @var \Concrete\Core\File\Image\BasicThumbnailer */
    private $thumbnailer;

    /** @var \Doctrine\ORM\EntityManagerInterface */
    private $entityManager;

    public function __construct(AtomicThumbnailStream $stream, ResponseFactory $factory, BasicThumbnailer $thumbnailer, EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->stream = $stream;
        $this->factory = $factory;
        $this->thumbnailer = $thumbnailer;
        $this->entityManager = $entityManager;
    }

    public function generate()
    {
        $built = false;
        $iterator = $this->stream->getIterator();
        if ($item = $iterator->current()) {
            $this->buildThumbnail($item);
            $built = true;
        }

        return $this->factory->json(['built' => $built, 'path' => $item['path']]);
    }

    private function buildThumbnail(array $item)
    {
        $fileRepository = $this->entityManager->getRepository(File::class);

        if (!$file = $fileRepository->findOneBy(['fID' => array_get($item, 'fileID', 0)])) {
            return;
        }

        if ($this->attemptBuild($file, $item)) {
            $this->completeBuild($file, $item);
        }
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
     * Mark the build complete.
     *
     * @param $file
     * @param $thumbnail
     */
    private function completeBuild(File $file, $thumbnail)
    {
        // Update the database to have "1" for isBuilt
        $this->entityManager->getConnection()->update('FileImageThumbnailPaths', ['isBuilt' => '1'], $thumbnail);
    }

    /**
     * Get the dimensions out of a thumbnail array.
     *
     * @param array $thumbnail
     *
     * @return null|array [ width, height, crop ]
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
        $thumbnailer = $this->thumbnailer;
        if (method_exists($thumbnailer, 'getStorageLocation')) {
            $thumbnailerLocation = $thumbnailer->getStorageLocation();
            if ($thumbnailerLocation instanceof StorageLocationInterface) {
                return $thumbnailerLocation;
            }
        }

        return null;
    }

}
