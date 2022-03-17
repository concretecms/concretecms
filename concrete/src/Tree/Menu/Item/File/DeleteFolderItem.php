<?php

namespace Concrete\Core\Tree\Menu\Item\File;

use Concrete\Core\Tree\Menu\Item\DeleteItem;

class DeleteFolderItem extends DeleteItem
{

    public function getItemName()
    {
        return t('Delete Folder');
    }

    public function getActionURL()
    {
        return \URL::to('/ccm/system/dialogs/tree/node/delete/file_folder?treeNodeID=' . $this->node->getTreeNodeID());
    }


}