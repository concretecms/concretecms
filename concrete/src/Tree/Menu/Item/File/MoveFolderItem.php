<?php
namespace Concrete\Core\Tree\Menu\Item\File;

use Concrete\Core\Support\Facade\Url as URL;

class MoveFolderItem extends FolderItem
{
    public function getDialogTitle()
    {
        return t('Move Folder');
    }

    public function getAction()
    {
        return 'move-node';
    }

    public function getItemName()
    {
        return t('Move Folder');
    }

    public function getActionURL()
    {
        return URL::to('/ccm/system/dialogs/tree/node/move/file_folder?treeNodeID=' . $this->folder->getTreeNodeID());
    }
}
