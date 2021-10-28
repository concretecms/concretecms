<?php
namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

use Concrete\Core\Tree\Node\Node;

class FileFolderItem extends AbstractItem
{
    public function getDisplayName()
    {
        return t('Page Template');
    }

    public function getContentObject()
    {
        $folderNodes = Node::getNodesOfType('file_folder');
        foreach ($folderNodes as $folderNode) {
            if ($folderNode->getTreeNodeDisplayPath() == $this->getReference()) {
                return $folderNode;
            }
        }
        return null;
    }

    public function getFieldValue()
    {
        if ($o = $this->getContentObject()) {
            return $o->getTreeNodeID();
        }
    }
}
