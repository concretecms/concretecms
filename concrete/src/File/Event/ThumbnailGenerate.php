<?php

namespace Concrete\Core\File\Event;

use Symfony\Component\EventDispatcher\Event as AbstractEvent;

class ThumbnailGenerate extends AbstractEvent
{
    /**
     * @var string
     */
    private $path;

    /**
     * @var \Concrete\Core\File\Image\Thumbnail\Type\Version
     */
    protected $type;

    /**
     * @param string $path Absolute path to the thumbnail
     * @param \Concrete\Core\File\Image\Thumbnail\Type\Version $type
     */
    public function __construct($path, $type)
    {
        $this->path = $path;
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return \Concrete\Core\File\Image\Thumbnail\Type\Version
     */
    public function getThumbnailType()
    {
        return $this->type;
    }
}
