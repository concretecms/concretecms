<?php
namespace Concrete\Core\File\Image\Thumbnail\Type;

use \Concrete\Core\File\Version as FileVersion;
use Core;

/**
 * Handles regular and retina thumbnails. e.g. Each thumbnail type has two versions of itself
 * the regular one and the 2x one.
 */
class Version
{

    protected $directoryName;
    protected $handle;
    protected $width;

    public function __construct($directoryName, $handle, $width)
    {
        $this->handle = $handle;
        $this->width = $width;
        $this->directoryName = $directoryName;
    }

    /**
     * @param mixed $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }

    /**
     * @return mixed
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @param mixed $width
     */
    public function setWidth($width)
    {
        $this->width = $width;
    }

    /**
     * @return mixed
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param mixed $directoryName
     */
    public function setDirectoryName($directoryName)
    {
        $this->directoryName = $directoryName;
    }

    /**
     * @return mixed
     */
    public function getDirectoryName()
    {
        return $this->directoryName;
    }

    public static function getByHandle($handle)
    {
        $list = Type::getVersionList();
        foreach($list as $version) {
            if ($version->getHandle() == $handle) {
                return $version;
            }
        }
    }


    public function getFilePath(FileVersion $fv)
    {
        $prefix = $fv->getPrefix();
        $filename = $fv->getFileName();
        $hi = Core::make('helper/file');
        $ii = Core::make('helper/concrete/file');
        $filename = $hi->replaceExtension($filename, 'jpg');
        return REL_DIR_FILES_THUMBNAILS . '/' . $this->getDirectoryName() . $ii->prefix($prefix, $filename);
    }

}