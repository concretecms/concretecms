<?php
namespace Concrete\Core\File\Image\Thumbnail\Type;

use Concrete\Core\File\Image\Thumbnail\Path\Resolver;
use Concrete\Core\Entity\File\Version as FileVersion;
use Concrete\Core\File\Image\Thumbnail\Type\Type as ThumbnailType;
use Core;
use Concrete\Core\Support\Facade\Application;

/**
 * Handles regular and retina thumbnails. e.g. Each thumbnail type has two versions of itself
 * the regular one and the 2x one.
 */
class Version
{
    protected $directoryName;
    protected $handle;
    protected $name;
    protected $sizingMode;
    protected $width;
    protected $height;
    protected $isDoubledVersion;

    public function __construct($directoryName, $handle, $name, $width, $height, $isDoubledVersion = false, $sizingMode = ThumbnailType::RESIZE_DEFAULT)
    {
        $this->handle = $handle;
        $this->name = $name;
        $this->sizingMode = $sizingMode;
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
     * @param mixed $sizingMode
     */
    public function setSizingMode($sizingMode)
    {
        $this->sizingMode = $sizingMode;
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

    /**
     * @return string
     */
    public function getSizingMode()
    {
        return $this->sizingMode;
    }

    public function getSizingModeDisplayName()
    {
        $sizingModeDisplayNames = [
            ThumbnailType::RESIZE_PROPORTIONAL => t("Proportional"),
            ThumbnailType::RESIZE_EXACT => t("Exact"),
        ];

        return $sizingModeDisplayNames[$this->getSizingMode()];
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
        $app = Application::getFacadeApplication();
        $hi = $app->make('helper/file');
        $ii = $app->make('helper/concrete/file');
        $thumbnailFormat = $app->make('config')->get('concrete.misc.default_thumbnail_format');
        $prefix = $fv->getPrefix();
        $filename = $fv->getFileName();
        switch ($thumbnailFormat) {
            case 'jpeg':
                $extension = 'jpg';
                break;
            case 'png':
                $extension = 'png';
                break;
            case 'auto':
            default:
                $extension = preg_match('/\.p?jpe?g$/i', $filename) ? 'jpg' : 'png';
                break;
        }

        return REL_DIR_FILES_THUMBNAILS . '/' . $this->getDirectoryName() . $ii->prefix($prefix, $hi->replaceExtension($filename, $extension));
    }
}
