<?php
namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\Tree\Node\Node as TreeNode;
use Concrete\Core\Tree\Node\Type\Formatter\CategoryListFormatter;
use Concrete\Core\Tree\Node\Type\Menu\CategoryMenu;
use Concrete\Core\Tree\Node\Type\Menu\FileFolderMenu;
use Loader;

class FileFolder extends Category
{

    public function getTreeNodeTypeName()
    {
        return t('Folder');
    }

    public function getTreeNodeMenu()
    {
        return new FileFolderMenu($this);
    }

    public function getTreeNodeJSON()
    {
        $node = parent::getTreeNodeJSON();
        if ($node) {
            $node->isFolder = true;
            $node->resultsThumbnailImg = $this->getListFormatter()->getIconElement();
        }
        return $node;
    }

    public function getTreeNodeName()
    {
        if ($this->getTreeNodeParentID() == 0) {
            return t('File Manager');
        }
        return parent::getTreeNodeName();
    }

}
