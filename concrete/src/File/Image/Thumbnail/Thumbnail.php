<?php

namespace Concrete\Core\File\Image\Thumbnail;

class Thumbnail
{
    /**
     * The thumbnail type version.
     *
     * @var \Concrete\Core\File\Image\Thumbnail\Type\Version
     */
    protected $version;

    /**
     * The thumbnail full URL (if available) or its path relative to the webroot.
     *
     * @var string
     */
    protected $path;

    /**
     * Initialize the instance.
     *
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version $version the thumbnail type version
     * @param string $path the thumbnail full URL (if available) or its path relative to the webroot
     */
    public function __construct(\Concrete\Core\File\Image\Thumbnail\Type\Version $version, $path)
    {
        $this->version = $version;
        $this->path = $path;
    }

    /**
     * Get the thumbnail type version.
     *
     * @return \Concrete\Core\File\Image\Thumbnail\Type\Version
     */
    public function getThumbnailTypeVersionObject()
    {
        return $this->version;
    }

    /**
     * Get the thumbnail full URL (if available) or its path relative to the webroot.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}
