<?php

namespace Concrete\Core\File\Image\Thumbnail\Type;

use Concrete\Core\File\Image\Thumbnail\Path\Resolver;
use Concrete\Core\File\Version as FileVersion;
use Core;

/**
 * Handles regular and retina thumbnails. e.g. Each thumbnail type has two versions of itself
 * the regular one and the 2x one.
 */
class Version
{
    protected $directoryName;
    protected $handle;
    protected $name;
    protected $width;
    protected $height;
    protected $isDoubledVersion;

    public function __construct($directoryName, $handle, $name, $width, $height, $isDoubledVersion = false)
    {
        $this->handle = $handle;
        $this->name = $name;
        $this->width = $width;
        $this->height = $height;
        $this->directoryName = $directoryName;
        $this->isDoubledVersion = (bool) $isDoubledVersion;
    }

    /**
     * @param mixed $handle
     */
    public function setHandle($handle)
    {
        $this->handle = $handle;
    }

    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @param mixed $name
     */
    public function setHeight($height)
    {
        $this->height = $height;
    }

    /**
     * @return mixed
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /** Returns the display name for this thumbnail type version (localized and escaped accordingly to $format)
     * @param string $format = 'html'
     *    Escape the result in html format (if $format is 'html').
     *    If $format is 'text' or any other value, the display name won't be escaped.
     *
     * @return string
     */
    public function getDisplayName($format = 'html')
    {
        $value = tc('ThumbnailTypeName', $this->getName());
        if ($this->isDoubledVersion) {
            $value = t('%s (Retina)', $value);
        }
        switch ($format) {
            case 'html':
                return h($value);
            case 'text':
            default:
                return $value;
        }
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
     * @return mixed
     */
    public function getHeight()
    {
        return $this->height;
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
        foreach ($list as $version) {
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
        $f1 = REL_DIR_FILES_THUMBNAILS . '/' . $this->getDirectoryName() . $ii->prefix($prefix, $filename);
        $f2 = REL_DIR_FILES_THUMBNAILS . '/' . $this->getDirectoryName() . $ii->prefix($prefix,
                $hi->replaceExtension($filename, 'jpg'));
        // 5.7.4 keeps extension; older sets it to .jpg

        $filesystem = $fv->getFile()->getFileStorageLocationObject()->getFileSystemObject();
        if ($filesystem->has($f1)) {
            return $f1;
        }

        //fallback
        return $f2;
    }
}
