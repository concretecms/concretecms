<?php

namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Entity\Package;
use Concrete\Core\Permission\Category;

defined('C5_EXECUTE') or die('Access Denied.');

class PermissionKeyCategory extends AbstractCategory
{
    public function getItemCategoryDisplayName()
    {
        return t('Permission Categories');
    }

    public function getItemName($category)
    {
        $txt = \Core::make('helper/text');

        return $txt->unhandle($category->getPermissionKeyCategoryHandle());
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\ItemCategory\AbstractCategory::getPackageItems()
     *
     * @return \Concrete\Core\Permission\Category[]
     */
    public function getPackageItems(Package $package)
    {
        return Category::getListByPackage($package);
    }
}
