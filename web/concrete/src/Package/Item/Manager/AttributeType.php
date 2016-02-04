<?php
namespace Concrete\Core\Package\Item\Manager;

use Concrete\Controller\Element\Package\AttributeTypeItemList;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;

defined('C5_EXECUTE') or die("Access Denied.");

class AttributeType extends AbstractItem
{

    public function getItemCategoryDisplayName()
    {
        return t('Attribute Types');
    }

    public function getItemName($type)
    {
        return $type->getAttributeTypeDisplayName();
    }

    public function renderList(Package $package)
    {
        $controller = new AttributeTypeItemList($this, $package);
        $controller->render();
    }

    public function getPackageItems(Package $package)
    {
        return Type::getListByPackage($package);
    }

}
