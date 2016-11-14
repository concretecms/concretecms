<?php

namespace Concrete\Core\File\Image\Thumbnail\Type;

use Concrete\Core\Entity\File\Version;
use Concrete\Core\File\Image\Thumbnail\Type\Version as ThumbnailVersion;

class CustomThumbnail extends ThumbnailVersion
{

    protected $path;

    /**
     * CustomThumbnail constructor.
     * @param int $width
     * @param int $height
     * @param string $path The full path to the file (whether it exists or not)
     */
    public function __construct($width, $height, $path, $cropped)
    {
        $width = intval($width);
        $height = intval($height);
        $cropped = intval($cropped);
        $this->path = $path;
        parent::__construct(REL_DIR_FILES_CACHE, "ccm_{$width}x{$height}_{$cropped}", 'Custom', $width, $height);
    }

    public function getFilePath(Version $fv)
    {
        return $this->path;
    }

}
