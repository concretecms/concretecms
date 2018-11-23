<?php
namespace Concrete\Core\Tree\Menu\Item\File;

use Concrete\Core\Tree\Menu\Item\AbstractItem;
use Concrete\Core\Tree\Node\Type\FileFolder;

abstract class FolderItem extends AbstractItem
{
    /**
     * @var \Concrete\Core\Tree\Node\Type\FileFolder
     */
    protected $folder;

    /**
     * FolderItem constructor.
     *
     * @param \Concrete\Core\Tree\Node\Type\FileFolder $folder
     */
    public function __construct(FileFolder $folder)
    {
        $this->folder = $folder;
    }
}
