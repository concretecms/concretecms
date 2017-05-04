<?php

namespace Concrete\Core\File\Image\Thumbnail\Type;

use Concrete\Core\Entity\File\Version as FileVersion;
use Concrete\Core\File\Image\Thumbnail\Type\Version as ThumbnailVersion;
use Concrete\Core\File\Image\Thumbnail\Type\Type as ThumbnailType;

class CustomThumbnail extends ThumbnailVersion
{

    protected $path;
    protected $cropped;
  
    /**
     * CustomThumbnail constructor.
     * @param int $width
     * @param int $height
     * @param string $path The full path to the file (whether it exists or not)
     * @param bool $cropped
     */
    public function __construct($width, $height, $path, $cropped)
    {
        $width = (int) $width;
        $height = (int) $height;
        $sizingMode = $cropped ? ThumbnailType::RESIZE_EXACT : ThumbnailType::RESIZE_PROPORTIONAL;
        $cropped = (int) $cropped;
        $this->path = $path;
        $this->cropped = (bool) $cropped;
        parent::__construct(REL_DIR_FILES_CACHE, "ccm_{$width}x{$height}_{$cropped}", 'Custom', $width, $height, false, $sizingMode);
    }

    public function getFilePath(FileVersion $fv)
    {
        return $this->path;
    }

    public function isCropped()
    {
        return $this->cropped;
    }

}
