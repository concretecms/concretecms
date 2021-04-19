<?php
namespace Concrete\Core\Tree\Menu\Item\Group;

use Concrete\Core\Tree\Menu\Item\AbstractItem;
use Concrete\Core\Tree\Node\Type\GroupFolder;

abstract class FolderItem extends AbstractItem
{
    /**
     * @var \Concrete\Core\Tree\Node\Type\GroupFolder
     */
    protected $folder;

    /**
     * FolderItem constructor.
     *
     * @param \Concrete\Core\Tree\Node\Type\GroupFolder $folder
     */
    public function __construct(GroupFolder $folder)
    {
        $this->folder = $folder;
    }
}
