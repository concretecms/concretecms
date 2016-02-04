<?php
namespace Concrete\Core\Package\Item\Manager;

use Concrete\Core\Entity\Package;
use Concrete\Core\Permission\Category;

defined('C5_EXECUTE') or die("Access Denied.");

class PermissionKeyCategory extends AbstractItem
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

    public function getPackageItems(Package $package)
    {
        return Category::getListByPackage($package);
    }

}
