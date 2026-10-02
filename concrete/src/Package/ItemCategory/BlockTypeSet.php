<?php

namespace Concrete\Core\Package\ItemCategory;

use Concrete\Core\Block\BlockType\Set;
use Concrete\Core\Entity\Package;

defined('C5_EXECUTE') or die('Access Denied.');

class BlockTypeSet extends AbstractCategory
{
    public function getItemCategoryDisplayName()
    {
        return t('Block Type Sets');
    }

    public function getItemName($set)
    {
        return $set->getBlockTypeSetDisplayName();
    }

    /**
     * {@inheritdoc}
     *
     * @see \Concrete\Core\Package\ItemCategory\AbstractCategory::getPackageItems()
     *
     * @return \Concrete\Core\Block\BlockType\Set[]
     */
    public function getPackageItems(Package $package)
    {
        return Set::getListByPackage($package);
    }
}
