<?php
namespace Concrete\Core\Tree\Node\Type\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\DropdownMenu;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Permission\Checker;
use Concrete\Core\Tree\Menu\Item\DeleteItem;
use Concrete\Core\Tree\Menu\Item\EditPermissionsItem;
use Concrete\Core\Tree\Node\Type\Group;
use Concrete\Core\User\User;

class GroupMenu extends DropdownMenu
{
    public function __construct(Group $group)
    {
        parent::__construct();
        $u = new User();
        $p = new Checker($group);
        if ($p->canEditTreeNode()) {
            $url = \URL::to('/dashboard/users/groups', 'edit', $group->getTreeNodeGroupID());
            $this->addItem(new LinkItem($url, t('Edit Group')));
        }
        if ($p->canEditTreeNodePermissions()) {
            $this->addItem(new EditPermissionsItem($group));
        }
    }

}