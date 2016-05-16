<?php
namespace Concrete\Core\File\Image;

use Concrete\Core\File\StorageLocation\StorageLocationInterface;
use Config;
use Image;
use Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use Concrete\Core\Entity\File\File;

class BasicThumbnailer
{
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
            $this->setStorageLocation(StorageLocation::getDefault());
        }
        return $this->storageLocation;
    }

    /**
     * @param StorageLocationInterface $storageLocation
     */
    public function setStorageLocation(StorageLocationInterface $storageLocation)
    {
        $this->storageLocation = $storageLocation;
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
     * Deprecated. Use the Image facade instead.
     *
     * @deprecated
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
        $storage = $this->getStorageLocation();
        $filesystem = $storage->getFileSystemObject();
        $configuration = $storage->getConfigurationObject();

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
        if (!$filesystem->has($abspath)) {
            if ($obj instanceof File) {
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

        $src = $configuration->getPublicURLToFile($abspath);
        $thumb = new \stdClass();
        $thumb->src = $src;
        //this is terrible
        try {
            //try and get it locally, otherwise use http
            $dimensions = getimagesize($abspath);
        } catch (\Exception $e) {
            $dimensions = getimagesize($src);
        }
        $thumb->width = $dimensions[0];
        $thumb->height = $dimensions[1];
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
