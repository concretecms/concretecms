<?php

namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Package;

defined('C5_EXECUTE') or die('Access Denied.');

class TreeType extends AbstractCategory
{
    public function getItemCategoryDisplayName()
    {
        return t('Tree Types');
    }

    /**
     * @param $type \Concrete\Core\Tree\TreeType
     *
     * @return mixed
     */
    public function getItemName($type)
    {
        return $type->getTreeTypeHandle();
    }

    public function getPackageItems(Package $package)
    {
        return \Concrete\Core\Tree\TreeType::getListByPackage($package);
    }
}
