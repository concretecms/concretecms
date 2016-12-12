<?php
namespace Concrete\Core\File\Image\Thumbnail;
class Thumbnail
{

    protected $version;
    protected $path;

    public function __construct(\Concrete\Core\File\Image\Thumbnail\Type\Version $version, $path)
    {
        $this->version = $version;
        $this->path = $path;
    }

    public function getThumbnailTypeVersionObject()
    {
        return $this->version;
    }

    public function getPath()
    {
        return $this->path;
    }

}