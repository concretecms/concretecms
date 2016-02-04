<?php
namespace Concrete\Core\Package\Item\Manager;

use Concrete\Controller\Element\Package\BlockTypeItemList;
use Concrete\Core\Block\BlockType\Set;
use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;

defined('C5_EXECUTE') or die("Access Denied.");

class BlockTypeSet extends AbstractItem
{

    public function getItemCategoryDisplayName()
    {
        return t('Block Type Sets');
    }

    public function getItemName($set)
    {
        return $set->getBlockTypeSetDisplayName();
    }

    public function renderList(Package $package)
    {
        $controller = new BlockTypeItemList($this, $package);
        $controller->render();
    }

    public function getPackageItems(Package $package)
    {
        return Set::getListByPackage($package);
    }

}
