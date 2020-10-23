<?php

namespace Concrete\Core\File\Component\Chooser\Option;

use Concrete\Core\Entity\File\Folder\FavoriteFolder;
use Concrete\Core\File\Component\Chooser\ChooserOptionInterface;
use Concrete\Core\File\Component\Chooser\OptionSerializableTrait;
use Concrete\Core\Tree\Node\Node;
use Concrete\Core\Tree\Node\Type\FileFolder;
use Exception;

class HomeFolderOption implements ChooserOptionInterface
{
    use OptionSerializableTrait;

    /** @var FileFolder */
    protected $treeNode;

    /**
     * HomeFolderOption constructor.
     * @param int $homeFolderId
     * @throws Exception
     */
    public function __construct($homeFolderId)
    {
        $this->treeNode = Node::getByID($homeFolderId);

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
        return t("Home Folder");
    }

}