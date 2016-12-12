<?php
namespace Concrete\Core\Tree\Node\Type\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Application\UserInterface\ContextMenu\Item\LinkItem;
use Concrete\Core\Tree\Menu\Item\CloneItem;
use Concrete\Core\Tree\Menu\Item\DeleteItem;
use Concrete\Core\Tree\Menu\Item\EditPermissionsItem;
use Concrete\Core\Tree\Menu\Item\Topic\EditTopicItem;
use Concrete\Core\Tree\Menu\Menu;
use Concrete\Core\Tree\Node\Type\Group;
use Concrete\Core\Tree\Node\Type\Topic;

class GroupMenu extends Menu
{

    public function __construct(Group $group)
    {
        parent::__construct($group);
        $p = new \Permissions($group);
        if ($p->canEditTreeNode()) {
            $url = \URL::to('/dashboard/users/groups', 'edit', $group->getTreeNodeGroupID());
            $this->addItem(new LinkItem($url, t('Edit Group')));
        }
        if ($p->canEditTreeNodePermissions()) {
            $this->addItem(new EditPermissionsItem($group));
        }
        if ($p->canDeleteTreeNode()) {
            $this->addItem(new DeleteItem($group));
        }
    }

}