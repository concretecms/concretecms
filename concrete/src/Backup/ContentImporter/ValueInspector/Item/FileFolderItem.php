<?php

namespace Concrete\Core\Backup\ContentImporter\ValueInspector\Item;

use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\FileFolder;

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
        if (preg_match('/^(?:(?<path>.*?):)?id=(?<id>[1-9][0-9]*)$/D', $reference, $m)) {
            $path = $m['path'] ?? '';
            $id = (int) $m['id'];
        } else {
            $path = $reference;
            $id = null;
        }
        if ($path !== '') {
            $folderNodes = Node::getNodesOfType('file_folder');
            foreach ($folderNodes as $folderNode) {
                if ($folderNode->getTreeNodeDisplayPath() === $path) {
                    return $folderNode;
                }
            }
        } elseif ($id !== null) {
            $folderNode = FileFolder::getByID($id);
            if ($folderNode instanceof FileFolder) {
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
