<?php

namespace Concrete\Core\File\Component\Chooser\Option;

use Concrete\Core\Entity\File\Folder\FavoriteFolder;
use Concrete\Core\File\Component\Chooser\ChooserOptionInterface;
use Concrete\Core\File\Component\Chooser\OptionSerializableTrait;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Exception;

class FolderBookmarkOption implements ChooserOptionInterface
{
    use OptionSerializableTrait;

    protected $favoriteFolder;
    /** @var FileFolder */
    protected $treeNode;

    /**
     * FolderBookmarkOption constructor.
     * @param FavoriteFolder $favoriteFolder
     * @throws Exception
     */
    public function __construct(FavoriteFolder $favoriteFolder)
    {
        $this->favoriteFolder = $favoriteFolder;

        $this->treeNode = Node::getByID($this->favoriteFolder->getTreeNodeFolderId());

        if (!$this->treeNode instanceof FileFolder) {
            throw new Exception(t("Invalid node type."));
        }
    }

    public function getId()
    {
        return $this->treeNode->getTreeNodeID();
    }

    public function getComponentKey(): string
    {
        return 'folder-bookmark';
    }

    public function getTitle(): string
    {
        return $this->treeNode->getTreeNodeName();
    }

}