<?php
namespace Concrete\Core\Tree\Menu\Item\Group;

use Concrete\Core\Support\Facade\Url as URL;

class EditFolderItem extends FolderItem
{
    public function getDialogTitle()
    {
        return t('Edit Folder');
    }

    public function getAction()
    {
        return 'edit-node';
    }

    public function getItemName()
    {
        return t('Edit Folder');
    }

    public function getActionURL()
    {
        return URL::to('/ccm/system/dialogs/tree/node/edit/group_folder?treeNodeID=' . $this->folder->getTreeNodeID());
    }
}
