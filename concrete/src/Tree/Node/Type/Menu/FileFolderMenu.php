<?php
namespace Concrete\Core\Tree\Node\Type\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Menu;
use Concrete\Core\Tree\Menu\Item\File\EditFolderItem;
use Concrete\Core\Tree\Menu\Item\DeleteItem;
use Concrete\Core\Tree\Menu\Item\EditPermissionsItem;
use Concrete\Core\Tree\Node\Type\FileFolder;

class FileFolderMenu extends Menu
{

    public function __construct(FileFolder $folder)
    {
        parent::__construct();
        $p = new \Permissions($folder);
        if ($p->canEditTreeNode()) {
            $this->addItem(new EditFolderItem($folder));
        }
        if ($p->canEditTreeNodePermissions() || $p->canDeleteTreeNode()) {
            $this->addItem(new DividerItem());
        }
        if ($p->canEditTreeNodePermissions() && \Config::get('concrete.permissions.model') != 'simple') {
            $this->addItem(new EditPermissionsItem($folder));
        }
        if ($p->canDeleteTreeNode()) {
            $this->addItem(new DeleteItem($folder));
        }
    }

}