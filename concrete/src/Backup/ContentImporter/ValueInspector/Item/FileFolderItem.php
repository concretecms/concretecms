<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

use Concrete\Core\Tree\Node\Node;

class FileFolderItem extends AbstractItem
{
    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getDisplayName()
     */
    public function getDisplayName()
    {
        return t('Page Template');
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getContentObject()
     *
     * @return \Concrete\Core\Tree\Node\Type\FileFolder|null
     */
    public function getContentObject()
    {
        $reference = (string) $this->getReference();
        $folderNodes = Node::getNodesOfType('file_folder');
        foreach ($folderNodes as $folderNode) {
            if ($folderNode->getTreeNodeDisplayPath() === $reference) {
                return $folderNode;
            }
        }

        return null;
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Backup\ContentImporter\ValueInspector\Item\ItemInterface::getFieldValue()
     *
     * @return int|null
     */
    public function getFieldValue()
    {
        $folderNode = $this->getContentObject();

        return $folderNode ? $folderNode->getTreeNodeID() : null;
    }
}
