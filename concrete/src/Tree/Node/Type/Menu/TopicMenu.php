<?php
namespace Concrete\Core\Tree\Node\Type\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Tree\Menu\Item\CloneItem;
use Concrete\Core\Tree\Menu\Item\DeleteItem;
use Concrete\Core\Tree\Menu\Item\EditPermissionsItem;
use Concrete\Core\Tree\Menu\Item\Topic\EditTopicItem;
use Concrete\Core\Tree\Menu\Menu;
use Concrete\Core\Tree\Node\Type\Topic;

class TopicMenu extends Menu
{

    public function __construct(Topic $topic)
    {
        parent::__construct($topic);
        $p = new \Permissions($topic);
        if ($p->canEditTreeNode()) {
            $this->addItem(new EditTopicItem($topic));
        }
        if ($p->canDuplicateTreeNode()) {
            $this->addItem(new CloneItem($topic));
        }
        if ($p->canEditTreeNodePermissions() || $p->canDeleteTreeNode()) {
            $this->addItem(new DividerItem());
        }
        if ($p->canEditTreeNodePermissions()) {
            $this->addItem(new EditPermissionsItem($topic));
        }
        if ($p->canDeleteTreeNode()) {
            $this->addItem(new DeleteItem($topic));
        }
    }

}