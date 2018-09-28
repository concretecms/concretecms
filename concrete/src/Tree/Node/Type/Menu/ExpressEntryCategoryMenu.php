<?php
namespace Concrete\Core\Tree\Node\Type\Menu;

use Concrete\Core\Application\UserInterface\ContextMenu\Item\DividerItem;
use Concrete\Core\Tree\Menu\Item\Category\AddCategoryItem;
use Concrete\Core\Tree\Menu\Item\Category\AddExpressEntryCategoryItem;
use Concrete\Core\Tree\Menu\Item\Category\AddExpressEntryResultsFolderItem;
use Concrete\Core\Tree\Menu\Item\Category\EditCategoryItem;
use Concrete\Core\Tree\Menu\Item\CloneItem;
use Concrete\Core\Tree\Menu\Item\Category\DeleteExpressEntryCategoryItem;
use Concrete\Core\Tree\Menu\Item\EditPermissionsItem;
use Concrete\Core\Tree\Menu\Item\Topic\AddTopicItem;
use Concrete\Core\Tree\Node\Type\Category;
use Concrete\Core\Tree\Menu\Menu;

class ExpressEntryCategoryMenu extends Menu
{

    public function __construct(Category $category)
    {
        parent::__construct($category);
        $p = new \Permissions($category);
        if ($p->canAddCategoryTreeNode()) {
            $this->addItem(new AddExpressEntryCategoryItem($category));
            /**
             * Note; this is currently unused because we don't actually give you
             * a way to create these nodes through the UI, they get automatically
             * created for you
             */
            //$this->addItem(new AddExpressEntryResultsFolderItem($category));
        }
        if ($p->canEditTreeNode()) {
            $this->addItem(new EditCategoryItem($category));
        }
        if ($p->canDuplicateTreeNode()) {
            $this->addItem(new CloneItem($category));
        }
        if ($p->canEditTreeNodePermissions() || $p->canDeleteTreeNode()) {
            $this->addItem(new DividerItem());
        }
        if ($p->canEditTreeNodePermissions()) {
            $this->addItem(new EditPermissionsItem($category));
        }
        if ($p->canDeleteTreeNode()) {
            $this->addItem(new DeleteExpressEntryCategoryItem($category));
        }
    }

}