<?php
namespace Concrete\Core\Tree\Node\Type\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Tree\Menu\Item\File\EditFolderItem;
use Concrete\Core\Tree\Menu\Item\File\DeleteFolderItem;
use Concrete\Core\Tree\Menu\Item\EditPermissionsItem;
use Concrete\Core\Tree\Menu\Item\File\MoveFolderItem;
use Concrete\Core\Tree\Node\Type\FileFolder;

class FileFolderMenu extends DropdownMenu
{

    public function __construct(FileFolder $folder)
    {
        parent::__construct();
        $p = new \Permissions($folder);
        if ($p->canEditTreeNode()) {
            $this->addItem(new EditFolderItem($folder));
            $this->addItem(new MoveFolderItem($folder));
        }
        if ($p->canEditTreeNode() && ($p->canEditTreeNodePermissions() || $p->canDeleteTreeNode())) {
            $this->addItem(new DividerItem());
        }
        if ($p->canEditTreeNodePermissions() && \Config::get('concrete.permissions.model') != 'simple') {
            $this->addItem(new EditPermissionsItem($folder));
        }
        if ($p->canDeleteTreeNode()) {
            $this->addItem(new DeleteFolderItem($folder));
        }
    }

}
