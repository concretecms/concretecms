<?php
namespace Concrete\Core\File\Image;

use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\Entity\File\StorageLocation\StorageLocation;
use Concrete\Core\File\Image\Thumbnail\Path\Resolver;
use Concrete\Core\File\Image\Thumbnail\ThumbnailerInterface;
use Concrete\Core\File\Image\Thumbnail\Type\CustomThumbnail;
use Concrete\Core\File\StorageLocation\Configuration\DefaultConfiguration;
use Concrete\Core\File\StorageLocation\StorageLocationInterface;
use Config;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Image;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Concrete\Core\Entity\File\File;

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
     * @return StorageLocationInterface
     */
    public function getStorageLocation()
    {
        if ($this->storageLocation === null) {
            /** @var EntityManagerInterface $orm */
            $orm = $this->app['database/orm']->entityManager();
            $storageLocation = $orm->getRepository(StorageLocation::class)->findOneBy([ 'fslIsDefault' => true ]);

            if ($storageLocation) {
                $this->storageLocation = $storageLocation;
            }
        }

        return $this->storageLocation;
    }

    /**
     * @param \Concrete\Core\File\StorageLocation\StorageLocationInterface $storageLocation
     * @return self
     */
    public function setStorageLocation(StorageLocationInterface $storageLocation)
    {
        $this->storageLocation = $storageLocation;
        return $this;
    }

    /**
     * Overrides the default or defined JPEG compression level per instance
     * of the image helper. This allows for a single-use for a particularly
     * low or high compression value. Passing a non-integer value will reset
     * to the default system setting (DEFINE or 80).
     *
     * @param int $level the level of compression
     * @return self
     */
    public function setJpegCompression($level)
    {
        if (is_int($level)) {
            $this->jpegCompression = min(max($level, 0), 100);
        }

        return $this;
    }

    protected function getJpegCompression()
    {
        if (!isset($this->jpegCompression)) {
            $this->jpegCompression = \Config::get('concrete.misc.default_jpeg_image_compression');
        }

        return $this->jpegCompression;
    }

    /**
     * Create a thumbnail
     * @param \Imagine\Image\ImagineInterface|string $mixed
     * @param string $newPath
     * @param int $width
     * @param int $height
     * @param bool $fit
     */
    public function create($mixed, $newPath, $width, $height, $fit = false)
    {
        $thumbnailOptions = array('jpeg_quality' => \Config::get('concrete.misc.default_jpeg_image_compression'));
        $filesystem = $this->getStorageLocation()
          ->getFileSystemObject();

        if ($mixed instanceof \Imagine\Image\ImageInterface) {
            $image = $mixed;
        } else {
            $image = Image::open($mixed);
        }
        if ($fit) {
            $thumb = $image->thumbnail(new Box($width, $height), ImageInterface::THUMBNAIL_OUTBOUND);
            $filesystem->write(
              $newPath,
              $thumb->get('jpeg', $thumbnailOptions)
            );

        } else {
            if ($height < 1) {
                $thumb = $image->thumbnail($image->getSize()->widen($width));
            } else if ($width < 1) {
                $thumb = $image->thumbnail($image->getSize()->heighten($height));
            } else {
                $thumb = $image->thumbnail(new Box($width, $height));
            }
            $filesystem->write(
              $newPath,
              $thumb->get('jpeg', $thumbnailOptions)
            );
        }
    }

    /**
     * Deprecated.
     */
    /**
     * Returns a path to the specified item, resized and/or cropped to meet max width and height. $obj can either be
     * a string (path) or a file object.
     * Returns an object with the following properties: src, width, height
     *
     * @param File|string $obj
     * @param int $maxWidth
     * @param int $maxHeight
     * @param bool $crop
     * @return \stdClass Object that has the following properties: src, width, height
     */
    public function getThumbnail($obj, $maxWidth, $maxHeight, $crop = false)
    {
        $storage = $obj->getFileStorageLocationObject();
        $this->setStorageLocation($storage);
        $filesystem = $storage->getFileSystemObject();
        $configuration = $storage->getConfigurationObject();
        $version = null;

        $fh = \Core::make('helper/file');
        if ($obj instanceof File) {
            try {
                $fr = $obj->getFileResource();
                $fID = $obj->getFileID();
                $filename = md5(implode(':', array($fID, $maxWidth, $maxHeight, $crop, $fr->getTimestamp())))
                  . '.' . $fh->getExtension($fr->getPath());
            } catch (\Exception $e) {
                $filename = '';
            }
        } else {
            $filename = md5(implode(':', array($obj, $maxWidth, $maxHeight, $crop, filemtime($obj))))
                . '.' . $fh->getExtension($obj);
        }

        $abspath = '/cache/' . $filename;

        $src = $configuration->getPublicURLToFile($abspath);

        /** Attempt to create the image */
        if (!$filesystem->has($abspath)) {
            if ($obj instanceof File && $fr->exists()) {
                $image = \Image::load($fr->read());
            } else {
                $image = \Image::open($obj);
            }
            // create image there
            $this->create($image,
                $abspath,
                $maxWidth,
                $maxHeight,
                $crop);
        }

        $thumb = new \stdClass();
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
            //try and get it locally, otherwise use http
            $dimensions = getimagesize($dimensionsPath);
            $thumb->width = $dimensions[0];
            $thumb->height = $dimensions[1];
        } catch (\Exception $e) {

        }

        return $thumb;
    }

    /**
     * Deprecated.
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
