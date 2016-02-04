<?php
namespace Concrete\Core\Package\Item\Manager;

use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;

defined('C5_EXECUTE') or die("Access Denied.");

class GroupSet extends AbstractItem
{

    public function getItemCategoryDisplayName()
    {
        return t('Group Sets');
    }

    public function getItemName($set)
    {
        return $set->getGroupSetDisplayName();
    }

    public function getPackageItems(Package $package)
    {
        return \Concrete\Core\User\Group\GroupSet::getListByPackage($package);
    }

}
