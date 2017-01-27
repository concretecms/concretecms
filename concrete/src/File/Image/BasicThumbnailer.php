<?php
namespace Concrete\Core\File\Image;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Entity\File\StorageLocation\StorageLocation;
use Concrete\Core\File\Image\Thumbnail\ThumbnailerInterface;
use Concrete\Core\File\StorageLocation\Configuration\DefaultConfiguration;
use Concrete\Core\File\StorageLocation\StorageLocationInterface;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Image;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Concrete\Core\Entity\File\File;
use Exception;
use stdClass;

class BasicThumbnailer implements ThumbnailerInterface, ApplicationAwareInterface
{
    use ApplicationAwareTrait;

    protected $jpegCompression;

    /**
     * @var StorageLocationInterface
     */
    private $storageLocation;

    public function __construct(StorageLocationInterface $storageLocation = null)
    {
        $this->storageLocation = $storageLocation;
    }

    /**
     * {@inheritdoc}
     *
     * @see ThumbnailerInterface::getStorageLocation()
     */
    public function getStorageLocation()
    {
        if ($this->storageLocation === null) {
            /** @var EntityManagerInterface $orm */
            $orm = $this->app['database/orm']->entityManager();
            $storageLocation = $orm->getRepository(StorageLocation::class)->findOneBy(['fslIsDefault' => true]);

            if ($storageLocation) {
                $this->storageLocation = $storageLocation;
            }
        }

        return $this->storageLocation;
    }

    /**
     * {@inheritdoc}
     *
     * @see ThumbnailerInterface::setStorageLocation()
     */
    public function setStorageLocation(StorageLocationInterface $storageLocation)
    {
        $this->storageLocation = $storageLocation;

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see ThumbnailerInterface::setJpegCompression()
     */
    public function setJpegCompression($level)
    {
        if (is_int($level) || is_float($level) || (is_string($level) && is_numeric($level))) {
            $this->jpegCompression = min(max((int) $level, 0), 100);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     *
     * @see ThumbnailerInterface::getJpegCompression()
     */
    public function getJpegCompression()
    {
        if (!isset($this->jpegCompression)) {
            $this->jpegCompression = (int) $this->app->make('config')->get('concrete.misc.default_jpeg_image_compression');
        }

        return $this->jpegCompression;
    }

    /**
     * {@inheritdoc}
     *
     * @see ThumbnailerInterface::create()
     */
    public function create($mixed, $savePath, $width, $height, $fit = false)
    {
        $thumbnailOptions = ['jpeg_quality' => $this->getJpegCompression()];
        $filesystem = $this->getStorageLocation()->getFileSystemObject();

        if ($mixed instanceof ImageInterface) {
            $image = $mixed;
        } else {
            $image = Image::open($mixed);
        }
        if ($fit) {
            $thumb = $image->thumbnail(new Box($width, $height), ImageInterface::THUMBNAIL_OUTBOUND);
            $filesystem->write(
                $savePath,
                $thumb->get('jpeg', $thumbnailOptions)
            );
        } else {
            if ($height < 1) {
                $thumb = $image->thumbnail($image->getSize()->widen($width));
            } elseif ($width < 1) {
                $thumb = $image->thumbnail($image->getSize()->heighten($height));
            } else {
                $thumb = $image->thumbnail(new Box($width, $height));
            }
            $filesystem->write(
                $savePath,
                $thumb->get('jpeg', $thumbnailOptions)
            );
        }
    }

    /**
     * {@inheritdoc}
     *
     * @see ThumbnailerInterface::getThumbnail()
     */
    public function getThumbnail($obj, $maxWidth, $maxHeight, $crop = false)
    {
        $storage = $obj->getFileStorageLocationObject();
        $this->setStorageLocation($storage);
        $filesystem = $storage->getFileSystemObject();
        $configuration = $storage->getConfigurationObject();
        $version = null;

        $fh = $this->app->make('helper/file');
        if ($obj instanceof File) {
            try {
                $fr = $obj->getFileResource();
                $fID = $obj->getFileID();
                $filename = md5(implode(':', [$fID, $maxWidth, $maxHeight, $crop, $fr->getTimestamp()]))
                    . '.' . $fh->getExtension($fr->getPath());
            } catch (Exception $e) {
                $filename = '';
            }
        } else {
            $filename = md5(implode(':', [$obj, $maxWidth, $maxHeight, $crop, filemtime($obj)]))
                . '.' . $fh->getExtension($obj);
        }

        $abspath = '/cache/' . $filename;

        $src = $configuration->getPublicURLToFile($abspath);

        /* Attempt to create the image */
        if (!$filesystem->has($abspath)) {
            if ($obj instanceof File && $fr->exists()) {
                $image = Image::load($fr->read());
            } else {
                $image = Image::open($obj);
            }
            // create image there
            $this->create($image,
                $abspath,
                $maxWidth,
                $maxHeight,
                $crop
            );
        }

        $thumb = new stdClass();
        $thumb->src = $src;

        // this is a hack, but we shouldn't go out on the network if we don't have to. We should probably
        // add a method to the configuration to handle this. The file storage locations should be able to handle
        // thumbnails.
        if ($configuration instanceof DefaultConfiguration) {
            $dimensionsPath = $configuration->getRootPath() . $abspath;
        } else {
            $dimensionsPath = $src;
        }

        try {
            $dimensions = @getimagesize($dimensionsPath);
        } catch (Exception $e) {
            $dimensions = false;
        }
        $thumb->width = ($dimensions === false) ? null : $dimensions[0];
        $thumb->height = ($dimensions === false) ?: $dimensions[1];

        return $thumb;
    }

    /**
     * @deprecated
     */
    public function outputThumbnail($mixed, $maxWidth, $maxHeight, $alt = null, $return = false, $crop = false)
    {
        $thumb = $this->getThumbnail($mixed, $maxWidth, $maxHeight, $crop);
        $html = '<img class="ccm-output-thumbnail" alt="' . $alt . '" src="' . $thumb->src . '" width="' . $thumb->width . '" height="' . $thumb->height . '" />';
        if ($return) {
            return $html;
        } else {
            echo $html;
        }
    }
}
