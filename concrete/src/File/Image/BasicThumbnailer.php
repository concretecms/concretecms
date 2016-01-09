<?php
namespace Concrete\Core\File\Image;

use Config;
use Image;
use Loader;
use \Imagine\Image\Box;
use Imagine\Image\ImageInterface;
use \Concrete\Core\File\File;

class BasicThumbnailer
{

    protected $jpegCompression;

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
            return $image->thumbnail(new Box($width, $height), ImageInterface::THUMBNAIL_OUTBOUND)->save($newPath, array('quality'=>$this->getJpegCompression()));

        } else {

            if ($height < 1) {
                $image->thumbnail($image->getSize()->widen($width))->save($newPath, array('quality'=>$this->getJpegCompression()));
            } else if ($width < 1) {
                $image->thumbnail($image->getSize()->heighten($height))->save($newPath, array('quality'=>$this->getJpegCompression()));
            } else {
                $image->thumbnail(new Box($width, $height))->save($newPath, array('quality'=>$this->getJpegCompression()));
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
            try {
                $fr = $obj->getFileResource();
              $fID = $obj->getFileID();
                $filename = md5(implode(':', array($fID, $maxWidth, $maxHeight, $crop, $fr->getTimestamp())))
                . '.' . $fh->getExtension($fr->getPath());
            } catch(\Exception $e) {
                $filename = '';
            }
        } else {
            $filename = md5(implode(':', array($obj, $maxWidth, $maxHeight, $crop, filemtime($obj))))
                . '.' . $fh->getExtension($obj);
        }

        if (!file_exists(Config::get('concrete.cache.directory') . '/' . $filename)) {
            if ($obj instanceof File) {
                $image = \Image::load($fr->read());
            } else {
                $image = \Image::open($obj);
            }
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
