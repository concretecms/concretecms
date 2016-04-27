<?php
namespace Concrete\Core\Tree\Node\Type;

use Concrete\Core\Tree\Node\Node as TreeNode;
use Concrete\Core\Tree\Node\Type\Formatter\CategoryListFormatter;
use Concrete\Core\Tree\Node\Type\Menu\CategoryMenu;
use Loader;

class FileFolder extends Category
{

    public function getTreeNodeName()
    {
        if ($this->getTreeNodeParentID() == 0) {
            return t('File Manager');
        }
        return parent::getTreeNodeName();
    }

}
