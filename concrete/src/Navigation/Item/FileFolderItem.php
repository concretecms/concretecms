<?php

namespace Concrete\Core\Navigation\Item;

use Concrete\Core\Tree\Node\Type\FileFolder;

/**
 * @method FileFolderItem[] getChildren()
 */
class FileFolderItem extends Item
{
    /**
     * @var int
     */
    protected $folderId;

    /**
     * Item constructor.
     *
     * @param FileFolder $folder
     */
    public function __construct(FileFolder $folder)
    {
        parent::__construct('', $folder->getTreeNodeDisplayName());

        $this->folderId = $folder->getTreeNodeID();
    }

    /**
     * @return int
     */
    public function getFolderId(): int
    {
        return $this->folderId;
    }

    /**
     * @param int $folderId
     */
    public function setFolderId(int $folderId): void
    {
        $this->folderId = $folderId;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize()
    {
        $data = parent::jsonSerialize();
        $data['folderId'] = $this->getFolderId();
        $data['type'] = 'file_folder';

        return $data;
    }
}
