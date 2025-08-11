<?php

namespace Concrete\Controller\Element\Files\Edit\ThumbnailEditor;

use Concrete\Core\Controller\ElementController;
use Concrete\Core\File\Image\Thumbnail\Type\Version;

class ConcreteThumbnailEditor extends ElementController
{

    /**
     * @var $thumbnail Version
     */
    protected $thumbnail;

    /**
     * @return Version
     */
    public function getThumbnail(): Version
    {
        return $this->thumbnail;
    }

    /**
     * @param Version $thumbnail
     */
    public function setThumbnail(Version $thumbnail): void
    {
        $this->thumbnail = $thumbnail;
    }

    public function getElement()
    {
        return 'files/edit/thumbnail_editor/concrete_thumbnail_editor';
    }

    public function view()
    {
        $this->set('thumbnail', $this->getThumbnail());
    }
}