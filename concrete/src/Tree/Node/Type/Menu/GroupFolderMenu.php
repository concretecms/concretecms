<?php
namespace Concrete\Core\Tree\Node\Type\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Tree\Menu\Item\DeleteItem;
use Concrete\Core\Tree\Menu\Item\EditPermissionsItem;
use Concrete\Core\Tree\Menu\Item\Group\EditFolderItem;
use Concrete\Core\Tree\Node\Type\GroupFolder;

class GroupFolderMenu extends DropdownMenu
{

    public function __construct(GroupFolder $folder)
    {
        parent::__construct();
        $p = new Checker($folder);
        if ($p->canEditTreeNode()) {
            $this->addItem(new EditFolderItem($folder));
        }
        if ($p->canEditTreeNodePermissions()) {
            $this->addItem(new EditPermissionsItem($folder));
        }
        if ($p->canDeleteTreeNode()) {
            $this->addItem(new DeleteItem($folder));
        }
    }

}
