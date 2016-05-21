<?php
namespace Concrete\Core\Package\ItemCategory;

use Concrete\Controller\Element\Package\AttributeTypeItemList;
use Concrete\Core\Attribute\Type;
use Concrete\Core\Entity\Package;
use Concrete\Controller\Element\Package\ThemeItemList;
use Doctrine\ORM\EntityManagerInterface;

defined('C5_EXECUTE') or die("Access Denied.");

class AttributeType extends AbstractCategory
{

    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function removeItem($type)
    {
        $this->entityManager->remove($type);
        $this->entityManager->flush();
    }

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
