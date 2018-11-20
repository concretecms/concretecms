<?php
namespace Concrete\Core\Tree\Menu\Item\File;

use Concrete\Core\Tree\Menu\Item\Category\EditCategoryItem;

class EditFolderItem extends EditCategoryItem
{
    public function getDialogTitle()
    {
        return t('Edit Folder');
    }

    public function getItemName()
    {
        return t('Edit Folder');
    }

    public function getActionURL()
    {
        return \URL::to('/ccm/system/dialogs/tree/node/edit/file_folder?treeNodeID=' . $this->category->getTreeNodeID());
    }
}
