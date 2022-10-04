<?php

namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Package;
use Concrete\Core\Tree\Node\NodeType;

defined('C5_EXECUTE') or die('Access Denied.');

class TreeNodeType extends AbstractCategory
{
    public function getItemCategoryDisplayName()
    {
        return t('Tree Node Types');
    }

    /**
     * @param $type NodeType
     *
     * @return mixed
     */
    public function getItemName($type)
    {
        return $type->getTreeNodeTypeHandle();
    }

    public function getPackageItems(Package $package)
    {
        return NodeType::getListByPackage($package);
    }
}
