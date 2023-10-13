<?php

namespace Concrete\Core\Navigation\Item;

use Concrete\Core\Tree\Node\Type\FileFolder;

class FileFolderItem implements ItemInterface, SerializableItemInterface
{
    /**
     * @var int
     */
    protected $folderId;

    /**
     * string
     */
    protected $name;

    /**
     * Item constructor.
     *
     * @param FileFolder $folder
     */
    public function __construct(FileFolder $folder)
    {
        $this->name = $folder->getTreeNodeDisplayName();
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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
        $data['folderId'] = $this->getFolderId();
        $data['type'] = 'file_folder';
        $data['name'] = $this->getName();
        $data['children'] = [];
        return $data;
    }
}
