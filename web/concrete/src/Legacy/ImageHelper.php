<?php
namespace Concrete\Core\Legacy;

use Config;
use Image;
use \Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use \Imagine\Image\Point\Center;
use \Concrete\Core\File\File;

class ImageHelper
{

    public $jpegCompression = 80;

    /**
     * Resets the compression level to the system default
     * This method is automatically run when Loader::helper invokes this class
     * @return void
     */
    function reset() {
        $this->jpegCompression = $this->defaultJpegCompression();
    }

    /**
     * Returns the default system value for JPEG image compression
     * @return int from 1-100
     */
    public function defaultJpegCompression(){
        return defined('AL_THUMBNAIL_JPEG_COMPRESSION') ? AL_THUMBNAIL_JPEG_COMPRESSION : 80;
    }

    /**
     * Overrides the default or defined JPEG compression level per instance
     * of the image helper. This allows for a single-use for a particularly
     * low or high compression value. Passing a non-integer value will reset
     * to the default system setting (DEFINE or 80)
     * @param int $level the level of compression
     * @return self
     */
    public function setJpegCompression($level) {
        if (is_int($level)) {
            $this->jpegCompression = min(max($level, 0), 100);
        } else {
            $this->reset();
        }
        return $this;
    }
    /**
     * Deprecated. Use the Image facade instead.
     * @deprecated
     */
    public function create($mixed, $newPath, $width, $height, $fit = false)
    {
        if ($mixed instanceof \Imagine\Image\ImageInterface) {
            $image = $mixed;
        } else {
            $image = Image::open($mixed);
        }
        if ($fit) {
            return $image->thumbnail(new Box($width, $height), ImageInterface::THUMBNAIL_OUTBOUND)->save($newPath);

        } else {

            if ($height < 1) {
                $image->thumbnail($image->getSize()->widen($width))->save($newPath);
            } else if ($width < 1) {
                $image->thumbnail($image->getSize()->heighten($height))->save($newPath);
            } else {
                $image->thumbnail(new Box($width, $height))->save($newPath);
            }
        }
     }

    /**
     * Deprecated
     */
    /**
     * Returns a path to the specified item, resized and/or cropped to meet max width and height. $obj can either be
     * a string (path) or a file object.
     * Returns an object with the following properties: src, width, height
     * @param mixed $obj
     * @param int $maxWidth
     * @param int $maxHeight
     * @param bool $crop
     */
    public function getThumbnail($obj, $maxWidth, $maxHeight, $crop = false) {
        $fID = false;
        $fh = Loader::helper('file');
        if ($obj instanceof File) {
            $fr = $obj->getFileResource();
            $image = \Image::load($fr->read());
            $fID = $obj->getFileID();
            $filename = md5(implode(':', array($fID, $maxWidth, $maxHeight, $crop, $fr->getTimestamp())))
                . '.' . $fh->getExtension($fr->getPath());
        } else {
            $image = \Image::open($obj);
            $filename = md5(implode(':', array($obj, $maxWidth, $maxHeight, $crop, filemtime($obj))))
                . '.' . $fh->getExtension($obj);
        }

        if (!file_exists(Config::get('concrete.cache.directory') . '/' . $filename)) {
            // create image there
            $this->create($image,
                          Config::get('concrete.cache.directory') . '/' . $filename,
                $maxWidth,
                $maxHeight,
                $crop);
        }

        $src = REL_DIR_FILES_CACHE . '/' . $filename;
        $abspath = Config::get('concrete.cache.directory') . '/' . $filename;
        $thumb = new \stdClass;
        if (isset($abspath) && file_exists($abspath)) {
            $thumb->src = $src;
            $dimensions = getimagesize($abspath);
            $thumb->width = $dimensions[0];
            $thumb->height = $dimensions[1];
            return $thumb;
        }
    }

    /**
     * Deprecated
     */
    public function outputThumbnail($mixed, $maxWidth, $maxHeight, $alt = null, $return = false, $crop = false) {
        $thumb = $this->getThumbnail($mixed, $maxWidth, $maxHeight, $crop);
        $html = '<img class="ccm-output-thumbnail" alt="' . $alt . '" src="' . $thumb->src . '" width="' . $thumb->width . '" height="' . $thumb->height . '" />';
        if ($return) {
            return $html;
        } else {
            print $html;
        }
    }

}
