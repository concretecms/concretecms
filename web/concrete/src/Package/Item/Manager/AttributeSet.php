<?php
namespace Concrete\Core\Package\Item\Manager;

use Concrete\Core\Attribute\Set;
use Concrete\Core\Entity\Attribute\Category;
use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;

defined('C5_EXECUTE') or die("Access Denied.");

class AttributeSet extends AbstractItem
{

    public function getItemCategoryDisplayName()
    {
        return t('Attribute Sets');
    }

    public function getItemName($set)
    {
        $at = Category::getByID($set->getAttributeSetKeyCategoryID());
        $txt = \Core::make('helper/text');
        return sprintf(
            '%s (%s)',
            $set->getAttributeSetDisplayName(),
            $txt->unhandle($at->getAttributeKeyCategoryHandle())
        );
    }

    public function getPackageItems(Package $package)
    {
        Set::getListByPackage($package);
    }

}
